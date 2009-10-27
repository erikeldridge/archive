<?php

/*
Copyright (c) 2009, Erik Eldridge. All rights reserved.
Code licensed under the BSD License:
http://github.com/erikeldridge/authproxy/blob/master/license.txt
*/

require 'keydb.php';
//post {domain}/authproxy/api.php?action=insert&hash={hash}&userId={userId}&type={type}
//   --> insert 
//   <-- record id
//post {domain}/authproxy/api.php?action=update&hash={hash}&userId={userId}&type={type}&recordId={recordId}
//   --> update 
//get {domain}/authproxy/api.php?action=select&hash={hash}&userId={userId}&type={type}&recordId={recordId} 
//   <-- record

$filters = array(
    
    //internal
    'hash' => FILTER_SANITIZE_STRING,
    'action' => FILTER_SANITIZE_STRING,
    'userId' => FILTER_SANITIZE_STRING,
    'recordId' => FILTER_SANITIZE_STRING,
    
    //oauth
    'providerName' => FILTER_SANITIZE_STRING,
    'consumerKey' => FILTER_SANITIZE_STRING,
    'consumerSecret' => FILTER_SANITIZE_STRING,
    'callbackUrl' => FILTER_SANITIZE_STRING,
);
$input = filter_var_array($_REQUEST, $filters);

if (!isset($input['userId'])) {
    $response = array('status' => 'error', 'details' => 'userId required');
} elseif (!array_key_exists($input['userId'], KeyDB::$credentials)) {
    $response = array('status' => 'error', 'details' => 'invalid userId: '.$input['userId']);
} elseif(sha1(KeyDB::$credentials[$input['userId']].$input['userId']) != $input['hash']) {
    $response = array('status' => 'error', 'details' => 'invalid hash: '.$input['hash']);
} elseif('oauth' == $input['type']) {
    $response = array('status' => 'error', 'details' => 'invalid type: '.$input['type']);
} elseif ('GET' != $_SERVER['REQUEST_METHOD'] && 'POST' != $_SERVER['REQUEST_METHOD']) {   
    $response = array('status' => 'error', 'details' => 'invalid request method: '.$_SERVER['REQUEST_METHOD']);
} elseif ('GET' == $_SERVER['REQUEST_METHOD']) {

    //init storage
    require '../netdb/sdk.php';
    $storage = new Netdb(KeyDB::$netdb_key, KeyDB::$netdb_secret);
    
    $storageKey = sprintf('%s-%s-%s', $input['userId'], $input['type'], $input['recordId']);
    $response = $storage->get($storageKey);

    //confirm success
    if('success' == $response->status){

        if (isset($response->value)) {
            $response = array('value' => json_decode($response->value));
        }
        
        $response['status'] = 'success';
        
    } else {
        $response = array('status' => 'error', 'debug' => $response);                
    }
    
} elseif (!in_array($input['action'], array('select', 'insert', 'update', 'delete'))) {
    $response = array('status' => 'error', 'details' => 'invalid action: '.$input['action']); 
} elseif ('POST' == $_SERVER['REQUEST_METHOD']) {
    
    //init storage
    require '../netdb/sdk.php';
    $storage = new Netdb(KeyDB::$netdb_key, KeyDB::$netdb_secret);
    
    switch ($input['action']) {
            
        case 'insert':

            //format for storage
            $obj = new stdclass();
            $obj->providerName = $input['providerName'];
            $obj->consumerKey = $input['consumerKey'];
            $obj->consumerSecret = $input['consumerSecret'];
            $obj->callbackUrl = $input['callbackUrl'];
            $storageValue = json_encode($obj);
        
            //store obj
            $recordId = sha1($storageValue.time());
            $storageKey = sprintf('%s-%s-%s', $input['userId'], $input['type'], $recordId);
            $response = $storage->set($storageKey, $storageValue);
        
            //confirm success
            if('success' == $response->status){
                $response = array('status' => 'success', 'recordId' => $recordId, 'debug' => print_r($input, true));
            } else {
                $response = array('status' => 'error', 'debug' => $response);                
            }
        
            break;
            
        case 'update':
        
            //format for storage
            $obj = new stdclass();
            $obj->openidRealmUri = $input['openidRealmUri'];
            $obj->openidReturnToUri = $input['openidReturnToUri'];
            $storageValue = json_encode($obj);
            
            //store obj
            $storageKey = sprintf('%s-%s-%s', $input['userId'], $input['type'], $input['recordId']);
            $response = $storage->set($storageKey, $storageValue);
            
            //confirm success
            if('success' == $response->status){
                $response = array('status' => 'success', 'debug' => $response);
            } else {
                $response = array('status' => 'error', 'debug' => $response);                
            }
            
            break;
            
        case 'delete':

            //store empty obj
            $storageKey = sprintf('%s-%s-%s', $input['userId'], $input['type'], $input['recordId']);
            $response = $storage->set($storageKey, '');

            //confirm success
            if('success' == $response->status){
                $response = array('status' => 'success', 'debug' => $response);
            } else {
                $response = array('status' => 'error', 'debug' => $response);                
            }

            break;
                
        default:
            $response = array('status' => 'error', 'details' => 'invalid action: '.$input['action']);
            break;
    }
} 

//output
echo json_encode($response);