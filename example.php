<?php

error_reporting(E_ALL);

require 'private.php';
require 'OauthPanda.class.php';

//constructs object in format expected by standard oauth library
function buildAccessTokenObject($oauth_token, $oauth_token_secret, $xoauth_yahoo_guid, $oauth_session_handle, $oauth_expires_in=null, $oauth_authorization_expires_in=null)
{
    $now = time();
    $access_token = new stdclass();
    $access_token->key = $oauth_token;
    $access_token->secret = $oauth_token_secret;
    $access_token->guid = $xoauth_yahoo_guid;
    $access_token->consumer = YAHOO_OAUTH_CONSUMER_KEY;
    $access_token->sessionHandle = $oauth_session_handle;

    // Check to see if the access token ever expires.
    if($oauth_expires_in) {
        $access_token->tokenExpires = $now + $oauth_expires_in;
    } else {
        $access_token->tokenExpires = -1;
    }

    // Check to see if the access session handle ever expires.
    if($oauth_authorization_expires_in) {
        $access_token->handleExpires = $now + $oauth_authorization_expires_in;
    } else {
        $access_token->handleExpires = -1;
    }
    
    return $access_token;
}

$foo = new OauthPanda(array(
    'request_client' => new YahooCurlWrapper,
    'oauth_client' => new StandardOauthWrapper,
    'consumer_key' => YAHOO_OAUTH_CONSUMER_KEY,
    'consumer_secret' => YAHOO_OAUTH_CONSUMER_SECRET
));

if (is_file('access_token.txt')) {
    
    //retrieve token
    $access_token = unserialize(file_get_contents('access_token.txt'));
    
    //attempt to request social for user corresponding w/ token
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
    
    $response_body = json_decode($response['response_body']);
        
    //if the token's expired, refresh it as per http://developer.yahoo.com/oauth/guide/oauth-auth-flow.html#oauth-refreshaccesstoken
    if (401 == $response['http_code'] && false !== strpos($response_body->error->description, 'token_expired')) {

        $response = $foo->set(array(
            'oauth_params_location' => 'url',
            'token' => $access_token
        ))->GET(array(
            'url' => 'https://api.login.yahoo.com/oauth/v2/get_token',
            'params' => array('oauth_session_handle' => $access_token->sessionHandle)
        ));
        
        parse_str($response['response_body'], $access_token_data);

        $access_token = buildAccessTokenObject(
            $access_token_data['oauth_token'], 
            $access_token_data['oauth_token_secret'], 
            $access_token_data['xoauth_yahoo_guid'], 
            $access_token_data['oauth_session_handle'], 
            $access_token_data['oauth_expires_in'], 
            $access_token_data['oauth_authorization_expires_in']
        );
        
        file_put_contents('access_token.txt', serialize($access_token));
        
        //retry request for social data
        $response = $foo->set(array(
            'oauth_params_location' => 'header',
            'token' => $access_token
        ))->GET(array(
            'url' => $url,
            'params' => $params
        ));
    
        $response_body = json_decode($response['response_body']);
        
    } 
    
    //do something w/ data ...
    var_dump($response_body);    

//if oauth verifier's in url, this is the callback from auth
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
    $access_token = buildAccessTokenObject(
        $access_token_data['oauth_token'], 
        $access_token_data['oauth_token_secret'], 
        $access_token_data['xoauth_yahoo_guid'], 
        $access_token_data['oauth_session_handle'], 
        $access_token_data['oauth_expires_in'], 
        $access_token_data['oauth_authorization_expires_in']
    );

    //store token for future usage
    file_put_contents('access_token.txt', serialize($access_token));
    
    //fetch data using token 
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
    
    $response_body = json_decode($response['response_body']);
    
    //do something w/ data ...
    var_dump($response_body);
    
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