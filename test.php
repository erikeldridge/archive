<?php
require 'keydb.php';
require '../curl/curl.php';
$curl = new Curl;

//insert oauth record
$userId = '1';
$url = sprintf('%s/authproxy/api.php?action=%s&hash=%s&userId=%s&type=%s',
    'http://localhost/~eldridge',
    'insert',
    sha1(KeyDB::$credentials[$userId].$userId),
    $userId,
    'oauth'
);
$params = array(
    'providerName' => 'yahoo.com',
    'consumerKey' => 'sjdjfljsldkjfkl',
    'consumerSecret' => 'skdjflsjdfjlsjdlkjflks',
    'callbackUrl' => 'http://example.com'
);
$response = json_decode($curl->post($url, $params)->body);
assert('success' == $response->status);
assert(isset($response->recordId) && !empty($response->recordId));

//delete record
$userId = '1';
$url = sprintf('%s/authproxy/api.php?action=%s&hash=%s&userId=%s&type=%s',
    'http://localhost/~eldridge',
    'delete',
    sha1(KeyDB::$credentials[$userId].$userId),
    $userId,
    'oauth'
);
$params = array(
    'recordId' => $response->recordId
);
$response = json_decode($curl->post($url, $params)->body);
assert('success' == $response->status);
?>