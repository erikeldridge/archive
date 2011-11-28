<?php
require '../yosdk/yahoo-yos-social-php5-86eef28/lib/OAuth/OAuth.php';
require '../yosdk/yahoo-yos-social-php5-86eef28/lib/Yahoo/YahooOAuthApplication.class.php';

//http://gist.github.com/387056
require 'MysqlUtil.php';

require 'config.php';

// safely fetch input
$notice = filter_var( $_GET['notice'], FILTER_SANITIZE_STRING );
$bbauth_token = filter_var( $_COOKIE['bbauth_token'], FILTER_SANITIZE_STRING );
$local_user_id = filter_var( $_COOKIE['local_user_id'], FILTER_SANITIZE_STRING );

// require bbauth session
if ( !$local_user_id ) {
    header( "Location: index.php?notice=session_required" );
}

// check for oauth token in storage
$db = new MysqlUtil( $db_host, $db_name, $db_user, $db_pass );

try {
    $results = $db->query(  
        "SELECT * FROM `oauth_tokens` 
        WHERE `local_user_id` = '%s' 
        AND `service` = 'yahoo' 
        LIMIT 0 , 1;",
        $local_user_id
    );
} catch ( Exception $e ) {
    printf( '<pre>%s</pre>', print_r( $e, true ) ); 
    die;
}

// there may be a record, but it may not have a valid token in it
if ( count( $results ) > 0 ){
    $access_token = json_decode( $results[0]['token_json'] );
}

// if there's a stored token, check if it's expired, and refresh if it is
if( $access_token && $access_token->expire_time < time() ){
    
    $oauth_app = new YahooOAuthApplication( $oauth_consumer_key, $oauth_consumer_secret, $oauth_application_id );
    
    $access_token = $oauth_app->refreshAccessToken( $access_token );
    $access_token->expire_time = time() + $access_token->expires_in;
    
    try {
        $results = $db->query( 
            "UPDATE `oauth_tokens` 
            SET `token_json` = '%s' 
            WHERE `service` = 'yahoo' 
            AND `local_user_id` = '%s', 
            LIMIT 1;",
            json_encode( $access_token ) , $local_user_id
        );
    } catch ( Exception $e ) {
        printf( '<pre>%s</pre>', print_r( $e, true ) ); 
        die;
    }
}

if( $access_token ){
    $oauth_app = new YahooOAuthApplication( $oauth_consumer_key, $oauth_consumer_secret, null, null, $access_token );
    
    //yql is awesome http://developer.yahoo.com/yql/console/?q=select%20*%20from%20social.profile%20where%20guid%3Dme
    $response = $oauth_app->yql('select * from social.profile where guid=me');
    
    $profile_data = $response->query->results->profile;
}
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"
    "http://www.w3.org/TR/html4/strict.dtd">
<html>
    <head>
        <link rel="stylesheet" href="stylesheet.css" type="text/css" media="screen" title="no title" charset="utf-8">
    </head>
    <body>
        
        <? if( !$access_token ): ?>
        
            <p class="notice yellow_notice">
                This site would like to personalize itself using your Yahoo! Profile data.  Click this button to started: <a href="upgrade.php" class="button">Upgrade me!</a>
            </p>
            
        <? elseif( 'upgrade_success' == $notice AND $access_token ): ?>
        
            <p class="notice green_notice">
                The upgrade was a massive success! &nbsp;Carry on.
            </p>
            
        <? endif ?>
        
        <p class="header" style="text-align:right">
            
            <? if( $profile_data ): ?>
                <span class="profile">
                    <img src="<?= $profile_data->image->imageUrl ?>" height="30" width="30"/>
                    <span class="name"><?= $profile_data->nickname ?></span>
                </span>
            <? endif ?>
            
            <a href="logout.php">Log out</a>
        </p>
        <p class="body">
            Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
        </p>
        
    </body>
</html>