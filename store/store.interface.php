<?php
interface Store {
    
    /**
    * retreives a value from storage
    * @param $key The index of the record to fetch as a string
    * @return raw contents of 'value' field
    * @throws exception if store error
    */
    function get($key);
    
    /**
    * puts a value into storage
    * @param $key The string index of the record to insert
    * @param $value The string content of the record to insert
    * @throws exception if store error
    */
    function set($key, $val);
}
?>