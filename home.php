<?php
require '../yosdk/yahoo-yos-social-php5-86eef28/lib/OAuth/OAuth.php';
require '../yosdk/yahoo-yos-social-php5-86eef28/lib/Yahoo/YahooOAuthApplication.class.php';
require 'MysqlUtil.php';
require 'config.php';

// safely fetch input
$notice = filter_var( $_REQUEST['notice'], FILTER_SANITIZE_STRING );
$bbauth_token = filter_var( $_REQUEST['bbauth_token'], FILTER_SANITIZE_STRING );
$local_user_id = filter_var( $_REQUEST['local_user_id'], FILTER_SANITIZE_STRING );

// require bbauth session
if ( !$local_user_id ) {
    header( "Location: index.php?notice=session_required" );
}

// check for oauth token in storage
$db = new MysqlUtil( $db_host, $db_name, $db_user, $db_pass );

$record = $db->select( array(
    'local_user_id' => $local_user_id, 
    'service' => 'yahoo' 
), 'oauth_tokens' );

// there may be a record, but it may not have a valid token in it
if ( $record ){
    $access_token = json_decode( $record['token_json'] );
}

// if there's a stored token, check if it's expired, and refresh if it is
if( $access_token && $access_token->expire_time < time() ){
    
    $oauth_app = new YahooOAuthApplication( $oauth_consumer_key, $oauth_consumer_secret, $oauth_application_id );
    
    $access_token = $oauth_app->refreshAccessToken( $access_token );
    $access_token->expire_time = time() + $access_token->expires_in;

    $db->update( 
        
        //set
        array( 'token_json' => json_encode( $access_token ) ),
        
        //where
        array(
            'local_user_id' => $local_user_id, 
            'service' => 'yahoo', 
            'service_user_id' => $access_token->yahoo_guid
        ), 
        
        //on table
        'oauth_tokens' 
    );
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