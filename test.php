<?php

/*
Copyright (c) 2009, Erik Eldridge. All rights reserved.
Code licensed under the BSD License:
http://github.com/erikeldridge/authproxy/blob/master/license.txt
*/

require 'keydb.php';

//http://github.com/shuber/curl
require '../curl/curl.php';
$curl = new Curl;

//insert oauth record
$userId = '1';
$providerName = 'yahoo.com';
$consumerKey = 'sjdfkjsjdlkfjl';
$consumerSecret = 'jljsldjfjsldjfljsjdf';
$callbackUrl = 'http://example.com';
$url = sprintf('%s/authproxy/api.php', 'http://localhost/~eldridge');
$params = array(
    'action' => 'insert',
    'hash' => sha1(KeyDB::$credentials[$userId].$userId),
    'userId' => $userId,
    'type' => 'oauth',
    'providerName' => $providerName,
    'consumerKey' => $consumerKey,
    'consumerSecret' => $consumerSecret,
    'callbackUrl' => $callbackUrl
);
$response = json_decode($curl->post($url, $params)->body);
assert('success' == $response->status);
assert(isset($response->recordId) && !empty($response->recordId));

//get oauth record
$recordId = $response->recordId;
$url = sprintf('%s/authproxy/api.php', 'http://localhost/~eldridge');
$params = array(
    'hash' => sha1(KeyDB::$credentials[$userId].$userId),
    'userId' => $userId,
    'type' => 'oauth',
    'recordId' => $recordId
);
$response = json_decode($curl->get($url, $params)->body);
assert('success' == $response->status);
$value = json_decode($response->value);
assert($value->providerName == $providerName);
assert($value->consumerKey == $consumerKey);
assert($value->consumerSecret == $consumerSecret);
assert($value->callbackUrl == $callbackUrl);

//delete record
$url = sprintf('%s/authproxy/api.php', 'http://localhost/~eldridge');
$params = array(
    'hash' => sha1(KeyDB::$credentials[$userId].$userId),
    'userId' => $userId,
    'action' => 'delete',
    'type' => 'oauth',
    'recordId' => $recordId
);
$response = json_decode($curl->post($url, $params)->body);
assert('success' == $response->status);
?>