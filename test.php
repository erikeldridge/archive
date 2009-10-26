<?php
require 'sdk.php';
require 'secure.inc';
$netdb = new Netdb($netdbUid, $netdbSecret);

$key = 'asd123';
$value = 'thisthat';
$response = $netdb->set($key, $value);
assert('success' == $response->status);
assert('thisthat' == $response->value);

$key = 'asd123';
$response = $netdb->get($key);
assert('success' == $response->status);
assert('thisthat' == $response->value);
?>