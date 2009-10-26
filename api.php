<?php
error_reporting(E_ALL);

/*
 CREATE TABLE `netdb`.`table1` (
`primary` INT( 20 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`key` VARCHAR( 20 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ,
`value` MEDIUMTEXT( 1000 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ,
`created` DATETIME NOT NULL ,
`updated` TIMESTAMP( 20 ) ON UPDATE CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE = MYISAM CHARACTER SET utf8 COLLATE utf8_unicode_ci
*/

//secure
require 'secure.inc';

//input
$filters = array(
    'uid' => FILTER_SANITIZE_STRING,
    'hash' => FILTER_SANITIZE_STRING,
    'key' => FILTER_SANITIZE_STRING,
    'value' => FILTER_SANITIZE_STRING
);
$input = filter_var_array($_REQUEST, $filters);

//gate
if($input['uid'] != $netdbUid){
    echo json_encode(array('status' => 'error', 'details' => 'invalid user id: '.$input['uid']));
    exit();
}elseif(md5($input['uid'].$netdbSecret) != $input['hash']){
    echo json_encode(array('status' => 'error', 'details' => 'invalid hash: '.$input['hash']));
    exit();
}elseif(empty($input['key'])){
    echo json_encode(array('status' => 'error', 'details' => 'key cannot be blank'.$input['key']));
    exit();
}

//init db
$dsn = "mysql:dbname=$name;host=$host";

try {
    $pdo = new PDO($dsn, $user, $pass);
} catch (PDOException $e) {
    echo json_encode(array('status' => 'error', 'details' => 'db connection failed: '.$e->getMessage()));
    exit();
}

//handle request
function get($pdo, $key){
    $sql = "SELECT value FROM `table1` WHERE `key` = :key";
    $prepared = $pdo->prepare($sql);
    $prepared->execute(array(':key' => $key));
    $result = $prepared->fetch();
    $response = array('status'=>'success');
    if($result){
        $response['value'] = $result['value'];
    }
    return $response;
}
switch($_SERVER['REQUEST_METHOD']){
    case 'GET':
        $response = get($pdo, $input['key']);
        break;
    case 'POST':
        $sql = "UPDATE `table1` SET `value` = :value WHERE `key` = :key";
        $prepared = $pdo->prepare($sql);
        $prepared->execute(array(':key' => $input['key'], ':value' => $input['value']));
        if(0 == $prepared->rowCount()){
            $sql = 'INSERT INTO `table1` (`primary`, `key`, `value`, `created`, `updated`) VALUES (NULL, :key, :value, NOW(), NOW())';
            $prepared = $pdo->prepare($sql);
            $prepared->execute(array(':key' => $input['key'], ':value' => $input['value']));
        }
        $response = get($pdo, $input['key']);
        break;
    default:
        $response = array('status' => 'error', 'details' => 'invalid request method: '.$_SERVER['REQUEST_METHOD']);
        break;
}

echo json_encode($response);
?>