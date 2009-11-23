<?php
// yahoo
define('YAHOO_OAUTH_CONSUMER_KEY', '');
define('YAHOO_OAUTH_CONSUMER_SECRET', '');

// twitter 
define('TWITTER_OAUTH_CONSUMER_KEY', '');
define('TWITTER_OAUTH_CONSUMER_SECRET', '');

define('OAUTH_CALLBACK_URL', '');

//http://github.com/yahoo/yos-social-php5/blob/master/lib/Yahoo/YahooCurl.class.php
$yahoo_php5_sdk_include_path = dirname(__FILE__).'{path to YahooCurl.class.php}';

//http://oauth.googlecode.com/svn/code/php/
$oauth_lib_include_path = dirname(__FILE__).'{path to OAuth.php}';

set_include_path(
    get_include_path().PATH_SEPARATOR
    .$yahoo_php5_sdk_include_path.PATH_SEPARATOR
    .$oauth_lib_include_path
);
?>