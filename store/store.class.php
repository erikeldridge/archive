<?php

class SqliteStore implements Store {
    function __construct($filePath) {
        $this->db = new SQLiteDatabase($filePath, 0777, $errorMessage);
    }
    function get($key) {
        $sql = sprintf(
            "SELECT value FROM table1 WHERE key = '%s';", 
            $key
        );
        
        $result = $this->db->query($sql)->fetch(SQLITE_ASSOC);
        if ($result) {

            //Cleanup filtered, json-ed, escaped data before returning it.
            $result = $result['value'];
        }
        return $result;
    }
    function set($key, $value){
        $time = time();
        $sql = sprintf(
            "REPLACE INTO 
            table1 (key, value, created, updated) 
            VALUES ('%s', '%s', '%d', '%d');", 
            $key, $value, $time, $time
        );
        
        //allow exceptions to bubble up
        $this->db->queryExec($sql);
    }
}


class MysqliStore implements Store {
    function __construct($host, $user, $pass, $name) {
        $this->mysqli = new mysqli($host, $user, $pass, $name);

        if ($mysqli->connect_error) {
            $details = sprintf(
                'db connection failed (%s): %s', 
                $this->mysqli->connect_errno, 
                $this->mysqli->connect_error
            );
            throw new Exception(json_encode(array('status' => 'error', 'details' => $details)));
        }
    }
    function runMultiQuery($mysqli, $sql){
        $results = array();
        if ($mysqli->multi_query($sql)) {
            do {
                $rows = array();
                if ($result = $mysqli->store_result()) {
                    while ($row = $result->fetch_assoc()) {
                        $rows[] = $row;
                    }
                    $result->free();
                }
                $results[] = $rows;
            } while ($mysqli->next_result());
        } else {
           //error?
        }
        return $results;
    }
    function get($key) {
        $sql = sprintf(
            "SELECT `value` FROM `table1` WHERE `key` = '%s';", 
            $key
        );
        
        //use multi query for consistency
        $result = runMultiQuery($mysqli, $sql);
        
        $response = array('status' => 'success');

        if ($result[0]) {
            
            //1st query (even single queries are nested), 1st result row, 'value field'.
            //Cleanup filtered, json-ed, escaped data before returning it.
            $response['value'] = html_entity_decode(stripslashes($result[0][0]['value']));
        }
    }
    function set($key, $val){}
}
?>