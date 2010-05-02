<?php
// ref http://github.com/yahoo/yos-social-php5
require '../yosdk/yahoo-yos-social-php5-86eef28/lib/OAuth/OAuth.php';
require '../yosdk/yahoo-yos-social-php5-86eef28/lib/Yahoo/YahooOAuthApplication.class.php';

//http://gist.github.com/387056
require 'MysqlUtil.php';

require 'config.php';

// use php5 sdk to simplify oauth dance
$oauth_app = new YahooOAuthApplication( $oauth_consumer_key, $oauth_consumer_secret, $oauth_application_id, $oauth_callback_uri );

// safely fetch input
$oauth_verifier = filter_var( $_GET['oauth_verifier'], FILTER_SANITIZE_STRING );
$local_user_id = filter_var( $_COOKIE['local_user_id'], FILTER_SANITIZE_STRING );
$request_token = filter_var( $_COOKIE[ $local_user_id.'_yahoo_rt' ], FILTER_SANITIZE_STRING );

// if user's not logged in, redirect back to index
if ( !$local_user_id ) {
    header( "Location: index.php?notice=session_required" );
}

// if verifier & stored token, we're in the redirect back from a successful auth
if ( $oauth_verifier && $request_token ) {
    
    // fetch request token (decode html entities from filter), & delete it
    $request_token = json_decode( stripslashes( html_entity_decode( $request_token ) ) );
    setcookie( $local_user_id.'_yahoo_rt', '', time() - 3600 );
    
    // exchange request token for access token
    $access_token = $oauth_app->getAccessToken( $request_token, $oauth_verifier );
    
    // calc time token will expire & add it to token obj
    $access_token->expire_time = time() + $access_token->expires_in;
    
    $db = new MysqlUtil( $db_host, $db_name, $db_user, $db_pass );
    
    $db->insert( array(
        'local_user_id' => $local_user_id, 
        'service' => 'yahoo', 
        'service_user_id' => $access_token->yahoo_guid, 
        'token_json' => json_encode( $access_token )
    ), 'oauth_tokens' );
    
    // redirect back to index w/ success message
    header( "Location: home.php?notice=upgrade_success" );
    
} else {
    $request_token = $oauth_app->getRequestToken( $oauth_callback_url );
    setcookie( $local_user_id.'_yahoo_rt', json_encode( $request_token ), time() + 600 );
    $redirect_url  = $oauth_app->getAuthorizationUrl($request_token);
    header( "Location: $redirect_url" );
}

?>