<?php

error_reporting(E_ALL);

require 'private.php';
require 'OauthPanda.class.php';

$foo = new OauthPanda(array(
    'request_client' => new YahooCurlWrapper,
    'oauth_client' => new StandardOauthWrapper,
    'consumer_key' => YAHOO_OAUTH_CONSUMER_KEY,
    'consumer_secret' => YAHOO_OAUTH_CONSUMER_SECRET
));

if (is_file('access_token.txt')) {
    
    //retrieve token
    $access_token = unserialize(file_get_contents('access_token.txt'));
    
    $url = "http://social.yahooapis.com/v1/users.guid($access_token->guid)/profile";
    $params = array(
        'format' => 'json'
    );
    $response = $foo->set(array(
        'oauth_params_location' => 'header',
        'token' => $access_token
    ))->GET(array(
        'url' => $url,
        'params' => $params
    ));
    
    var_dump($response);
    
} elseif (isset($_GET['oauth_verifier'])) {
    
    //fetch token
    $request_token = unserialize(file_get_contents('request_token.txt'));
    
    //exchange request token for access token
    $response = $foo->set(array(
        'oauth_params_location' => 'post',
        'token' => $request_token
    ))->POST(array(
        'url' => 'https://api.login.yahoo.com/oauth/v2/get_token',
        'params' => array('oauth_verifier' => $_GET['oauth_verifier'])
    ));
    
    parse_str($response['response_body'], $access_token_data);

    //sanity check
    assert(isset($access_token_data['oauth_token']));
    
    //format access token obj as expected by std oauth lib
    $now = time();
    $access_token = new stdclass();
    $access_token->key = $access_token_data["oauth_token"];
    $access_token->secret = $access_token_data["oauth_token_secret"];
    $access_token->guid = $access_token_data["xoauth_yahoo_guid"];
    $access_token->consumer = YAHOO_OAUTH_CONSUMER_KEY;
    $access_token->sessionHandle = $access_token_data["oauth_session_handle"];

    // Check to see if the access token ever expires.
    if(array_key_exists("oauth_expires_in", $access_token_data)) {
        $access_token->tokenExpires = $now + $access_token_data["oauth_expires_in"];
    } else {
        $access_token->tokenExpires = -1;
    }

    // Check to see if the access session handle ever expires.
    if(array_key_exists("oauth_authorization_expires_in", $access_token_data)) {
        $access_token->handleExpires = $now + $access_token_data["oauth_authorization_expires_in"];
    } else {
        $access_token->handleExpires = -1;
    }

    //store token for future usage
    file_put_contents('access_token.txt', serialize($access_token));
    
    //fetch data using token 
    $url = 'http://query.yahooapis.com/v1/public/yql';
    $params = array(
        'q' => 'select%20*%20from%20social.profile%20where%20guid%3Dme',
        'format' => 'json'
    );
    $response = $foo->set(array(
        'oauth_params_location' => 'header',
        'token' => $access_token
    ))->GET(array(
        'url' => $url,
        'params' => $params
    ));
    var_dump($response);
} else {
    //get request token
    $response = $foo->GET(array(
        'url' => 'https://api.login.yahoo.com/oauth/v2/get_request_token',
        'params' => array('oauth_callback' => OAUTH_CALLBACK_URL)
    ));

    //extract token
    parse_str($response['response_body'], $request_token_response);

    //sanity check
    assert(isset($request_token_response['oauth_token']));

    //standard oauth lib expects request token stdclass obj
    $request_token = (object) array(
        'key' => $request_token_response['oauth_token'],
        'secret' => $request_token_response['oauth_token_secret']
    );

    //cache token for retreival after auth
    file_put_contents('request_token.txt', serialize($request_token));

    //redirect user for auth
    $redirect_url = sprintf(
        'https://api.login.yahoo.com/oauth/v2/request_auth?oauth_token=%s&oauth_callback=%s',
    	$request_token_response['oauth_token'], 
    	urlencode(OAUTH_CALLBACK_URL)
    );
    header('Location: '.$redirect_url);
}

?>