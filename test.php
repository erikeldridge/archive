<?php

/*
Copyright (c) 2009, Erik Eldridge. All rights reserved.
Code licensed under the BSD License:
http://github.com/erikeldridge/authproxy/blob/master/license.txt
*/

//private
$users = array(''=>'');
$netdbKey = '';

//http://github.com/shuber/curl
require '../curl/curl.php';
$curl = new Curl;

//BEGIN: set up
//set up storage for indipendant auditing
require '../netdb/sdk.php';
require '../kvstore/interface.php';
// require '../kvstore/netbd/netdb.php';
// $storage = new NetDBStore(KeyDB::$netdb_key, KeyDB::$netdb_secret);
require '../kvstore/sqlite/sqlite.php';
$storage = new SQLiteStore($netdbKey);

//create user
$userId = '';
$storageKey = sprintf('authproxy-users-%s', $userId);
$value = new stdclass();
$response = $storage->set($storageKey, json_encode($value));

//BEGIN: insert oauth record
$authproxyUrl = sprintf('%s/authproxy/api.php', 'http://localhost/~eldridge');
$providerName = 'yahoo.com';
$consumerKey = 'sjdfkjsjdlkfjl';
$consumerSecret = 'jljsldjfjsldjfljsjdf';
$callbackUrl = 'http://example.com';
$params = array(
    'action' => 'insert',
    'hash' => sha1($users[$userId].$userId),
    'userId' => $userId,
    'type' => 'oauth',
    'providerName' => $providerName,
    'consumerKey' => $consumerKey,
    'consumerSecret' => $consumerSecret,
    'callbackUrl' => $callbackUrl
);
$response = json_decode($curl->post($authproxyUrl, $params)->body);
assert('success' == $response->status);
assert(isset($response->recordId) && !empty($response->recordId));

//audit service record in db
$recordId = $response->recordId;

$storageKey = sprintf('authproxy-services-%s-%s', $userId, $recordId);
$response = $storage->get($storageKey);
$value = json_decode($response);
assert($value->providerName == $providerName);
assert($value->consumerKey == $consumerKey);
assert($value->consumerSecret == $consumerSecret);
assert($value->callbackUrl == $callbackUrl);

//audit user record in db
$storageKey = sprintf('authproxy-users-%s', $userId);
$response = $storage->get($storageKey);
$value = json_decode($response);
assert(in_array($recordId, $value->recordIds));

//END: insert oauth record

//get oauth record
$url = sprintf('%s/authproxy/api.php', 'http://localhost/~eldridge');
$params = array(
    'hash' => sha1($users[$userId].$userId),
    'userId' => $userId,
    'type' => 'oauth',
    
    //record id defined on insertion
    'recordId' => $recordId
);

$response = json_decode($curl->get($url, $params)->body);
assert('success' == $response->status);
assert($response->value->providerName == $providerName);
assert($response->value->consumerKey == $consumerKey);
assert($response->value->consumerSecret == $consumerSecret);
assert($response->value->callbackUrl == $callbackUrl);

//BEGIN: delete record
$url = sprintf('%s/authproxy/api.php', 'http://localhost/~eldridge');
$params = array(
    'hash' => sha1($users[$userId].$userId),
    'userId' => $userId,
    'action' => 'delete',
    'type' => 'oauth',
    'recordId' => $recordId
);
$response = json_decode($curl->post($url, $params)->body);
assert('success' == $response->status);

//audit service record in db
$storageKey = sprintf('authproxy-services-%s-%s', $userId, $recordId);
$response = $storage->get($storageKey);
$value = json_decode($response->value);
assert(empty($value));

//audit user record in db
$storageKey = sprintf('authproxy-users-%s', $userId);
$response = $storage->get($storageKey);
$value = json_decode($response);
assert(!in_array($recordId, $value->recordIds));
//BEGIN: delete record

?>