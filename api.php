<?php

/*
Copyright (c) 2009, Erik Eldridge. All rights reserved.
Code licensed under the BSD License:
http://github.com/erikeldridge/authproxy/blob/master/license.txt
*/

//private
$netdbKey = '';
$netdbSecret = '';

$users = array(''=>'');

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
    'type' => FILTER_SANITIZE_STRING,
    
    //oauth
    'providerName' => FILTER_SANITIZE_STRING,
    'consumerKey' => FILTER_SANITIZE_STRING,
    'consumerSecret' => FILTER_SANITIZE_STRING,
    'callbackUrl' => FILTER_SANITIZE_STRING,
);
$input = filter_var_array($_REQUEST, $filters);

if (!isset($input['userId'])) {
    $response = array('status' => 'error', 'details' => 'userId required');
} elseif (!array_key_exists($input['userId'], $users)) {
    $response = array('status' => 'error', 'details' => 'invalid userId: '.$input['userId']);
} elseif(sha1($users[$input['userId']].$input['userId']) != $input['hash']) {
    $response = array('status' => 'error', 'details' => 'invalid hash: '.$input['hash']);
} elseif('oauth' != $input['type']) {
    $response = array('status' => 'error', 'details' => 'invalid type: '.$input['type']);
} elseif ('GET' != $_SERVER['REQUEST_METHOD'] && 'POST' != $_SERVER['REQUEST_METHOD']) {   
    $response = array('status' => 'error', 'details' => 'invalid request method: '.$_SERVER['REQUEST_METHOD']);
} elseif ('GET' == $_SERVER['REQUEST_METHOD']) {

    //init storage
    require '../curl/curl.php';
    require '../netdb/sdk.php';
    require '../kvstore/interface.php';
    // require '../kvstore/netbd/netdb.php';
    // $storage = new NetDBStore(KeyDB::$netdb_key, KeyDB::$netdb_secret);
    require '../kvstore/sqlite/sqlite.php';
    $storage = new SQLiteStore($netdbKey);
    
    $storageKey = sprintf('authproxy-services-%s-%s', $input['userId'], $input['recordId']);
    $value = $storage->get($storageKey);

    //confirm success
    if($value){
        $response = array('status' => 'success', 'value' => json_decode($value));  
    } else {
        $response = array('status' => 'error');                
    }
    
} elseif (!in_array($input['action'], array('select', 'insert', 'update', 'delete'))) {
    $response = array('status' => 'error', 'details' => 'invalid action: '.$input['action']); 
} elseif ('POST' == $_SERVER['REQUEST_METHOD']) {
    
    //init storage
    require '../curl/curl.php';
    require '../netdb/sdk.php';
    require '../kvstore/interface.php';
    // require '../kvstore/netbd/netdb.php';
    // $storage = new NetDBStore(KeyDB::$netdb_key, KeyDB::$netdb_secret);
    require '../kvstore/sqlite/sqlite.php';
    $storage = new SQLiteStore($netdbKey);
    // $storage->db->query("DROP TABLE $netdbKey");
    // $storage->db->query(sprintf("CREATE TABLE %s (key varchar(100) PRIMARY KEY, value varchar(1000), created timestamp(20))", $netdbKey), $error);
            
    switch ($input['action']) {
            
        case 'insert':

            //format for storage
            $obj = new stdclass();
            $obj->providerName = $input['providerName'];
            $obj->consumerKey = $input['consumerKey'];
            $obj->consumerSecret = $input['consumerSecret'];
            $obj->callbackUrl = $input['callbackUrl'];
            $storageValue = json_encode($obj);
        
            $recordId = sha1($storageValue.time());
            
            //add user id to record id just in case
            $storageKey = sprintf('authproxy-services-%s-%s', $input['userId'], $recordId);
            
            $response = $storage->set($storageKey, $storageValue);
            
            //add record id to credentials array in meta for user
            $storageKey = sprintf('authproxy-users-%s', $input['userId']);
            $response = $storage->get($storageKey);
    
            //sanity check
            if(!$response){
                $response = array('status' => 'error', 'debug' => 'there is no record for user '.$input['userId']);
                break;            
            }
        
            $value = json_decode($response);
            $value->recordIds[] = $recordId;
            
            try {
                $storage->set($storageKey, json_encode($value));
                $response = array('status' => 'success', 'recordId' => $recordId, 'debug' => print_r($value, true));
            } catch (Exception $e) {
                $response = array('status' => 'error', 'debug' => print_r($e, true));
            }
        
            break;
            
        case 'update':
        
            //format for storage
            $obj = new stdclass();
            $obj->openidRealmUri = $input['openidRealmUri'];
            $obj->openidReturnToUri = $input['openidReturnToUri'];
            $storageValue = json_encode($obj);
            
            //store obj
            $storageKey = sprintf('authproxy-services-%s-%s', $input['userId'], $input['recordId']);
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
            $storageKey = sprintf('authproxy-services-%s-%s', $input['userId'], $input['recordId']);
            $response = $storage->set($storageKey, '');

            //remove record id from services array in meta for user
            $storageKey = sprintf('authproxy-users-%s', $input['userId']);
            $response = $storage->get($storageKey);
            $value = json_decode($response);
            $index = array_search($input['recordId'], $value->recordIds);
            unset($value->recordIds[$index]);
            
            try {
                $response = $storage->set($storageKey, json_encode($value));
                $response = array('status' => 'success', 'debug' => print_r($value->recordIds, true));
            } catch (Exception $e) {
                $response = array('status' => 'error', 'debug' => print_r($e, true));
            }

            break;
                
        default:
            $response = array('status' => 'error', 'details' => 'invalid action: '.$input['action']);
            break;
    }
} 

//output
echo json_encode($response);