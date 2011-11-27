<?php
require '../interface.php';
require 'sqlite.php';

$tableName = 'footable';
$store = new SQLiteStore($tableName);

//set up
//$store->db->query("DROP TABLE $tableName");
$store->db->query("CREATE TABLE $tableName (key varchar(100) PRIMARY KEY, value varchar(1000), created timestamp(20))", $error);

//get non-existent key
$key = 'asd123';
$value = $store->get($key);
assert(false === $value);

//test insertion
$value = 'bar';
$store->set($key, $value);
$result = $store->get($key);
assert($value == $result);

//test replace
$value = 'baz';
$store->set($key, $value);
$result = $store->get($key);
assert($value == $result);

//test json insertion
$obj = new stdclass();
$obj->arr = array('this'=>'that');
$value = json_encode($obj);
$store->set($key, $value);
$result = $store->get($key);
assert($value == $result);

//clean up
$store->db->query("DROP TABLE $tableName");
