<?php
require 'sdk.php';
require 'secure.inc';
$netdb = new Netdb($netdbUid, $netdbSecret);

//test set
$key = 'asd123';
$value = 'thisthat';
$response = $netdb->set($key, $value);
assert('success' == $response->status);
assert('thisthat' == $response->value);

//test get
$key = 'asd123';
$response = $netdb->get($key);
assert('success' == $response->status);
assert('thisthat' == $response->value);

//test update
$key = 'asd123';
$value = 'fooboo';
$response = $netdb->set($key, $value);
assert('success' == $response->status);
assert('fooboo' == $response->value);
?>