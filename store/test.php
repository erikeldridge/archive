<?php

//Note: dir must be writable by web server for sqlite to be able to create a db and table

require 'store.interface.php';
require 'store.class.php';

$store = new SqliteStore('sqlite');

//set up
$store->db->query("CREATE TABLE table1 (key varchar(100), value varchar(1000), created timestamp(20), updated timestamp(20))", $error);

//test insertion
$store->set('foo', 'bar');
$result = $store->get('foo');
assert('bar' == $result);

//clean up
$store->db->query('DROP TABLE table1');
?>