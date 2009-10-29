<?php
require 'sdk.php';
require 'secure.inc';

//http://github.com/shuber/curl
require '../curl/curl.php';

$netdb = new NetDB($netdbUid, $netdbSecret);

$key = 'doesntexist';
$response = $netdb->get($key);
assert('success' == $response->status);
assert(!isset($response->value));

$key = 'asd123';
$value = 'thisthat';
$response = $netdb->set($key, $value);
assert('success' == $response->status);
assert('thisthat' == $response->value);

$key = 'asd123';
$response = $netdb->get($key);
assert('success' == $response->status);
assert('thisthat' == $response->value);

$key = 'asd123';
$value = 'fooboo';
$response = $netdb->set($key, $value);
assert('success' == $response->status);
assert('fooboo' == $response->value);
?>