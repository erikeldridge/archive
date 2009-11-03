<?php

class SQLiteStore implements Store {
    function __construct($filePath = 'sqlite') {
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



?>