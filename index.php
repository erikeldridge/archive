<?php

// using "Quickstart Package" http://developer.yahoo.com/auth/ 
require 'ybrowserauth.class.php5';

require 'config.php';

// safely fetch input
$notice = filter_var( $_GET['notice'], FILTER_SANITIZE_STRING );
$bbauth_token = filter_var( $_GET['token'], FILTER_SANITIZE_STRING );
$bbauth_userhash = filter_var( $_REQUEST['userhash'], FILTER_SANITIZE_STRING );
$local_user_id = filter_var( $_REQUEST['local_user_id'], FILTER_SANITIZE_STRING );

$bbauth_app = new YahooMailJSONRPC( $bbauth_application_id, $bbauth_consumer_secret );
$auth_url = $bbauth_app->getAuthURL( null, true );

if ( $local_user_id ) {
    header( "Location: home.php" );
    exit;
} elseif( $bbauth_userhash ) {
    setcookie( 'local_user_id', $bbauth_userhash );
    setcookie( 'bbauth_token', $bbauth_token );
    header( "Location: home.php" );
    exit;
}
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"
    "http://www.w3.org/TR/html4/strict.dtd">
<html>
    <head>
        <link rel="stylesheet" href="stylesheet.css" type="text/css" media="screen" title="no title" charset="utf-8">
    </head>
    <body>
        
        <? if( 'session_required' == $notice ): ?>

            <p class="notice red_notice">
                You'll need to log to continue
            </p>

        <? endif ?>
        
        <p class="header">
            <a href="<?= $auth_url ?>"><img src="http://l.yimg.com/a/i/reg/openid/buttons/12.png"/></a>
        </p>
        
        <p>
            Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
        </p>
        
    </body>
</html>