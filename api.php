<?php
error_reporting(E_ALL);

/* 
 CREATE TABLE `netdb`.`46c785c3c2f6de9199cdfed3225b87b2399d2592` (
`key` VARCHAR( 150 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ,
`value` VARCHAR( 1000 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ,
`created` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ,
) ENGINE = MYISAM CHARACTER SET utf8 COLLATE utf8_unicode_ci
*/

//import private vars
require 'secure.inc';

//keys for input
$keys = array('uid', 'hash', 'key');

//fetch input args from request uri
$uri = filter_var(
    $_SERVER['REQUEST_URI'], 
    FILTER_SANITIZE_STRING
);

//remove leading and trailing slashes so we can explode correctly
$uri = trim($uri, '/');

//split string into values
$values = explode('/', $uri);

//we don't want the leading 'netdb' or 'api'
$values = array_slice($values, 2);

//assemble the input into an assoc arr
$input = array_combine($keys, $values);

//validate input
if(!array_key_exists($input['uid'], $credentials)){
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
try {
    $mysqli = new mysqli($host, $user, $pass, $name);
} catch(Exception $e) {
    $details = sprintf('db connection failed (%s): %s', print_r($e, true));
    echo json_encode(array('status' => 'error', 'details' => $details));
    exit();
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

//handle requests
switch($_SERVER['REQUEST_METHOD']){
    case 'GET':
        $sql = sprintf(
            "SELECT `value` FROM `%s` WHERE `key` = '%s';",
            $input['uid'], 
            $input['key']
        );
    
        //use multi query for consistency
        $result = runMultiQuery($mysqli, $sql);
    
        $response = array('status' => 'success');

        if ($result[0]) {
        
            //1st query (even single queries are nested), 1st result row, 'value field'.
            //Cleanup filtered, json-ed, escaped data before returning it.
            $response['value'] = html_entity_decode(stripslashes($result[0][0]['value']));
        }     
        break;
        
    case 'POST':    
        if (!isset($_POST['value'])) {
            //error
        }
        $input['value'] = filter_var($_POST['value'], FILTER_SANITIZE_STRING);
        $sql = sprintf(
            "REPLACE INTO 
            `%s` (`key`, `value`, `created`) 
            VALUES ('%s', '%s', '%s');", 
            $input['uid'], $input['key'], $input['value'], time()
        );
        $result = runMultiQuery($mysqli, $sql);
        $response = array(
            'status' => 'success'
        );
        break;
        
    default:
        $response = array('status' => 'error', 'details' => 'invalid request method: '.$_SERVER['REQUEST_METHOD']);
        break;
}

$mysqli->close();

echo json_encode($response);
?>