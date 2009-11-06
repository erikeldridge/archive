<?php
/*
CREATE TABLE $tableName (key varchar(100), value varchar(1000), created timestamp(20)
*/
class SQLiteStore implements KVStore {
    function __construct($tableName, $filePath = 'sqlite') {
        $this->tableName = $tableName;
        $this->db = new SQLiteDatabase($filePath, 0777, $errorMessage);
    }
    function get($key) {
        $sql = sprintf(
            "SELECT value FROM %s WHERE key = '%s';", 
            $this->tableName,
            $key
        );
        $result = $this->db->query($sql)->fetch(SQLITE_ASSOC);
        if ($result) {
            $result = $result['value'];
        }
        return $result;
    }
    function set($key, $value){
        $time = time();
        $sql = sprintf(
            "REPLACE INTO 
            %s (key, value, created) 
            VALUES ('%s', '%s', '%d');", 
            $this->tableName, $key, $value, $time
        );
        
        //allow exceptions to bubble up
        $this->db->queryExec($sql);
    }
}



?>