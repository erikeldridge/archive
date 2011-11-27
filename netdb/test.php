<?php

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