<?php
error_reporting(E_ALL);

/*
 CREATE TABLE `netdb`.`table1` (
`primary` INT( 20 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`key` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ,
`value` MEDIUMTEXT( 1000 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ,
`created` DATETIME NOT NULL ,
`updated` TIMESTAMP( 20 ) ON UPDATE CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE = MYISAM CHARACTER SET utf8 COLLATE utf8_unicode_ci
*/

//import private vars
require 'secure.inc';

//filter input
$filters = array(
    'uid' => FILTER_SANITIZE_STRING,
    'hash' => FILTER_SANITIZE_STRING,
    'key' => FILTER_SANITIZE_STRING,
    'value' => FILTER_SANITIZE_STRING
);
$input = filter_var_array($_REQUEST, $filters);

//validate input
if(in_array($input['uid'], $credentials)){
    echo json_encode(array('status' => 'error', 'details' => 'invalid user id: '.$input['uid']));
    exit();
}elseif(sha1($credentials[$input['uid']].$input['uid']) != $input['hash']){
    echo json_encode(array('status' => 'error', 'details' => 'invalid hash: '.$input['hash']));
    exit();
}elseif(empty($input['key'])){
    echo json_encode(array('status' => 'error', 'details' => 'key cannot be blank'.$input['key']));
    exit();
}elseif(strlen($input['key']) > 150){
    echo json_encode(array('status' => 'error', 'details' => 'key must be <= 150 char, not '.strlen($input['key'])));
    exit();
}

//init db
$mysqli = new mysqli($host, $user, $pass, $name);

if ($mysqli->connect_error) {
    $details = sprintf('db connection failed (%s): %s', $mysqli->connect_errno, $mysqli->connect_error);
    echo json_encode(array('status' => 'error', 'details' => $details));
    exit();
}

//define fn to manage myqli multi_query
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

//handle requests
switch($_SERVER['REQUEST_METHOD']){
    case 'GET':
        $sql = sprintf(
            "SELECT `value` FROM `table1` WHERE `key` = '%s';", 
            $input['key']
        );
        
        //use multi query for consistency
        $result = runMultiQuery($mysqli, $sql);
        $response = array(
            'status' => 'success',
            
            //1st query (even single queries are nested), 1st result row, 'value field'.
            //Cleanup filtered, json-ed, escaped data before returning it.
            'value' => html_entity_decode(stripslashes($result[0][0]['value']))
        );
        break;
    case 'POST':
    
        //See if there's a record for this key.
        $sql = sprintf(
            "SELECT COUNT(*) FROM `table1` 
            WHERE `key` = '%s';", 
            $input['key']
        );
        $result = $mysqli->query($sql)->fetch_row();
        $input['value'] = $mysqli->escape_string($input['value']);
        
        //If there isn't a record.
        if (0 == $result[0]) {
            $sql = sprintf(
                
                //we use INSERT and not REPLACE because we don't use key as the primary key
                "INSERT INTO 
                `table1` (`primary`, `key`, `value`, `created`, `updated`) 
                VALUES (NULL, '%s', '%s', NOW(), NOW());", 
                $input['key'], $input['value']
            );
            
        //If there is a record.
        }else{
            $sql = sprintf(
                "UPDATE `table1`
                SET `value` = '%s'
                WHERE `key` = '%s';",
                $input['value'], $input['key']
            );
        }

        //Either way, return the record.
        $sql .= sprintf(
            "SELECT `value` FROM `table1` WHERE `key` = '%s';", 
            $input['key']
        );
        $result = runMultiQuery($mysqli, $sql);
        $response = array(
            'status' => 'success',
            'value' => html_entity_decode(stripslashes($result[1][0]['value']))
        );
        break;
    default:
        $response = array('status' => 'error', 'details' => 'invalid request method: '.$_SERVER['REQUEST_METHOD']);
        break;
}

$mysqli->close();

echo json_encode($response);
?>