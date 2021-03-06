<?php
interface KVStore {
    
    /**
    * retrieves a value from storage
    * @param $key The index of the record to fetch as a string
    * @return raw contents of 'value' field
    * @throws exception if store error
    */
    function get($key);
    
    /**
    * puts a value into storage
    * @param $key The string index of the record to insert
    * @param $value The raw (each storage type may have different reqs for escaping) string content of the record to insert
    * @throws exception if store error
    */
    function set($key, $value);
}
?>