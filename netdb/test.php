<?php
require '../../curl/curl.php';
require '../../netdb/sdk.php';
require '../interface.php';
require 'netdb.php';

$key = '';
$secret = '';
$store = new NetDBStore($key, $secret);

//get non-existent key
$key = 'jskldjfkljlksj';
$result = $store->get($key);
assert('success' == $result->status);
assert(empty($result->value));

//test insertion
$value = 'bar';
$result = $store->set($key, $value);
assert('success' == $result->status);
$result = $store->get($key);
assert($value == $result->value);

//clean up
$store->set($key, '');