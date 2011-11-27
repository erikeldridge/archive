<?php
<<<<<<< HEAD

//http://github.com/shuber/curl
require '../curl/curl.php';

require 'storage/interface.php';
require 'storage/sqlite.php';
require 'sdk.php';
require 'secure.inc';

$netdb = new NetDB('http://localhost/~eldridge', 1, $credentials[1]);

$key = 'doesntexist';
$response = $netdb->get($key);var_dump($response);
assert('success' == $response->status);
assert(!isset($response->value));
// 
// $key = 'asd123';
// $value = 'thisthat';
// $response = $netdb->set($key, $value);
// assert('success' == $response->status);
// 
// $key = 'asd123';
// $response = $netdb->get($key);
// assert('success' == $response->status);
// assert('thisthat' == $response->value);
// 
// $key = 'asd123';
// $value = 'fooboo';
// $response = $netdb->set($key, $value);
// assert('success' == $response->status);
// assert('fooboo' == $response->value);


?>
=======
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
>>>>>>> b4f9b893631b09d5f8a8ca3a5cad60a9e1f5e013
