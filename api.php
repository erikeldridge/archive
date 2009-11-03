<?php
error_reporting(E_ALL);

/* CREATE TABLE `netdb`.`46c785c3c2f6de9199cdfed3225b87b2399d2592` ( `key` VARCHAR( 150 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL PRIMARY KEY, `value` MEDIUMTEXT( 1000 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL , `created` DATETIME NOT NULL , `updated` TIMESTAMP( 20 ) ON UPDATE CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ) ENGINE = MYISAM CHARACTER SET utf8 COLLATE utf8_unicode_ci */

//import private vars
require 'secure.inc';
require 'storage/interface.php';
// require 'storage/mysqli.php';
require 'storage/sqlite.php';

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
try {
    $storage = new SQLiteStore();
} catch(Exception $e) {
    $details = sprintf('db connection failed (%s): %s', print_r($e, true));
    echo json_encode(array('status' => 'error', 'details' => $details));
    exit();
}

//handle requests
switch($_SERVER['REQUEST_METHOD']){
    case 'GET':
        try {
            $result = $storage->get($input['key']);
            $response = array('status' => 'success');
            if ($result) {
                
                //Cleanup filtered, json-ed, escaped data before returning it.
                $response['value'] = html_entity_decode(stripslashes($result));
            }
        } catch(Exception $e) {
            
        }        
        break;
        
    case 'POST':    
        try {
            $storage->set($input['key'], $input['value']);
            $response = array('status' => 'success');
        } catch(Exception $e) {
            $response = array('status' => 'error', 'details' => print_r($e, true));
        }
        break;
    default:
        $response = array('status' => 'error', 'details' => 'invalid request method: '.$_SERVER['REQUEST_METHOD']);
        break;
}

echo json_encode($response);
?>