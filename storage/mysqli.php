<?php
class MysqliStore implements Store {
    function __construct($host, $user, $pass, $name) {
        $this->mysqli = new mysqli($host, $user, $pass, $name);

        if ($this->mysqli->connect_error) {
            $details = sprintf(
                'db connection failed (%s): %s', 
                $this->mysqli->connect_errno, 
                $this->mysqli->connect_error
            );
            throw new Exception($details);
        }
    }
    function runMultiQuery($sql){
        $results = array();
        if ($this->mysqli->multi_query($sql)) {
            do {
                $rows = array();
                if ($result = $this->mysqli->store_result()) {
                    while ($row = $result->fetch_assoc()) {
                        $rows[] = $row;
                    }
                    $result->free();
                }
                $results[] = $rows;
            } while ($this->mysqli->next_result());
        } else {
           //error?
        }
        return $results;
    }
    function get($key) {
        $sql = sprintf(
            "SELECT `value` FROM `46c785c3c2f6de9199cdfed3225b87b2399d2592` WHERE `key` = '%s';", 
            $key
        );
        
        //use multi query for consistency
        $result = $this->runMultiQuery($sql);
        
        $response = array('status' => 'success');

        if ($result[0]) {
            
            //1st query (even single queries are nested), 1st result row, 'value field'.
            //Cleanup filtered, json-ed, escaped data before returning it.
            $response['value'] = html_entity_decode(stripslashes($result[0][0]['value']));
        }
    }
    function set($key, $value){
        $time = time();
        $sql = sprintf(
            "REPLACE INTO 
            `46c785c3c2f6de9199cdfed3225b87b2399d2592` (`key`, `value`, `created`) 
            VALUES ('%s', '%s', '%s');", 
            $key, $value, $time
        );

        $result = $this->runMultiQuery($sql);
        $response = array(
            'status' => 'success'
        );
    }
}
