<?php
require 'sdk.php';

$secret = '123qweasdzxc';
$uid = '1';
$netdb = new Netdb($uid, $secret);

$key = 'asd123';
$value = 'value=thisthat';
$response = $netdb->set($key, $value);
assert('success' == $response->status);

$key = 'asd123';
$response = $netdb->get($key);
assert('success' == $response->status);
assert('thisthat' == $response->value);
?>