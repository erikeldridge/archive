<?php

error_reporting(E_ALL);

require '../private.php';
require '../OauthPanda.class.php';

//ref http://apiwiki.twitter.com/Twitter-API-Documentation
$request_token_url = 'http://twitter.com/oauth/request_token';
$access_token_url = 'http://twitter.com/oauth/access_token';
$authentication_url = 'http://twitter.com/oauth/authenticate';

$foo = new OauthPanda(array(
    'request_client' => new ShuberCurlWrapper('../../curl/curl.php'),
    'oauth_client' => new StandardOauthWrapper('../OAuth.php'),
    'consumer_key' => TWITTER_OAUTH_CONSUMER_KEY,
    'consumer_secret' => TWITTER_OAUTH_CONSUMER_SECRET
));

if (is_file('twitter_access_token.txt')) {
    
    //retrieve token
    $access_token = unserialize(file_get_contents('twitter_access_token.txt'));
    
    $url = sprintf("http://twitter.com/statuses/user_timeline/%s.json", $access_token->screen_name);

    $response = $foo->set(array(
        'oauth_params_location' => 'url',
        'token' => $access_token
    ))->GET(array(
        'url' => $url
    ));
    
    $response_body = json_decode($response->body);
    
    //do something w/ data ...
    var_dump($response_body);  

} elseif (isset($_GET['oauth_verifier'])) {
    
    //fetch token
    $request_token = unserialize(file_get_contents('twitter_request_token.txt'));
    
    //exchange request token for access token
    $response = $foo->set(array(
        'oauth_params_location' => 'post',
        'token' => $request_token
    ))->POST(array(
        'url' => $access_token_url,
        'params' => array('oauth_verifier' => $_GET['oauth_verifier'])
    ));

    parse_str($response->body, $access_token_data);

    //sanity check
    assert(isset($access_token_data['oauth_token']));
    
    //format access token obj as expected by std oauth lib
    $access_token = new stdclass();
    $access_token->key = $access_token_data['oauth_token'];
    $access_token->secret = $access_token_data['oauth_token_secret'];
    $access_token->user_id = $access_token_data['user_id'];
    $access_token->screen_name = $access_token_data['screen_name'];

    //store token for future usage
    file_put_contents('twitter_access_token.txt', serialize($access_token));
    
    //fetch data using token 
    $url = sprintf("http://twitter.com/statuses/user_timeline/%s.json", $access_token_data['screen_name']);

    $response = $foo->set(array(
        'oauth_params_location' => 'url',
        'token' => $access_token
    ))->GET(array(
        'url' => $url
    ));

    $response_body = json_decode($response->body);
        
    //do something w/ data ...
    var_dump($response_body);

} else {
    
    //get request token
    $response = $foo->set(array(
        'oauth_params_location' => 'url'
    ))->GET(array(
        'url' => $request_token_url,
        'params' => array('oauth_callback' => OAUTH_CALLBACK_URL)
    ));

    //extract token
    parse_str($response->body, $request_token_response);

    //sanity check
    assert(isset($request_token_response['oauth_token']));

    //standard oauth lib expects request token stdclass obj
    $request_token = (object) array(
        'key' => $request_token_response['oauth_token'],
        'secret' => $request_token_response['oauth_token_secret']
    );

    //cache token for retreival after auth
    file_put_contents('twitter_request_token.txt', serialize($request_token));
    
    //redirect user for auth
    $redirect_url = sprintf(
        '%s?oauth_token=%s&oauth_callback=%s',
        $authentication_url,
    	$request_token_response['oauth_token'], 
    	urlencode(OAUTH_CALLBACK_URL)
    );
    header('Location: '.$redirect_url);
}

?>