<?php
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
// See if there's a record for this key.
// $sql = sprintf(
//     "SELECT COUNT(*) FROM `table1` 
//     WHERE `key` = '%s';", 
//     $input['key']
// );
// $result = $mysqli->query($sql)->fetch_row();
// $input['value'] = $mysqli->escape_string($input['value']);
// 
// //If there isn't a record.
// if (0 == $result[0]) {
//     $sql = sprintf(
//         
//         //we use INSERT and not REPLACE because we don't use key as the primary key
//         "INSERT INTO 
//         `table1` (`primary`, `key`, `value`, `created`, `updated`) 
//         VALUES (NULL, '%s', '%s', NOW(), NOW());", 
//         $input['key'], $input['value']
//     );
//     
// //If there is a record.
// }else{
//     $sql = sprintf(
//         "UPDATE `table1`
//         SET `value` = '%s'
//         WHERE `key` = '%s';",
//         $input['value'], $input['key']
//     );
// }
// 
// //Either way, return the record.
// $sql .= sprintf(
//     "SELECT `value` FROM `table1` WHERE `key` = '%s';", 
//     $input['key']
// );
// $result = runMultiQuery($mysqli, $sql);
// $response = array(
//     'status' => 'success',
//     'value' => html_entity_decode(stripslashes($result[1][0]['value']))
// );