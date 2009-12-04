<?php
error_reporting(E_ALL);

require 'private.php';
require 'OauthPanda.class.php';

// //test no args
// new OauthPanda;

//test invalid setting name
// $foo = new OauthPanda(array(
//  'blah'=>'baz'
// ));
// assert(false);

//test invalid setting value
// new OauthPanda(array(
//  'exception_handling'=>'bar'
// ));
// assert(false);

//test required setting
// new OauthPanda(array(
//  'exception_handling'=> 'print'
// ));
// assert(false);

//test default setting value
$foo = new OauthPanda(array(
    'request_client' => new stdclass,
    'oauth_client' => new StandardOauthWrapper,
    'consumer_key' => YAHOO_OAUTH_CONSUMER_KEY,
    'consumer_secret' => YAHOO_OAUTH_CONSUMER_SECRET
));
assert('print' == $foo->class_settings['exception_handling']['value']);

//test custom setting value
$foo = new OauthPanda(array(
    'exception_handling' => 'throw',
    'request_client' => new YahooCurlWrapper,
    'oauth_client' => new StandardOauthWrapper,
    'consumer_key' => YAHOO_OAUTH_CONSUMER_KEY,
    'consumer_secret' => YAHOO_OAUTH_CONSUMER_SECRET
));
assert('throw' == $foo->class_settings['exception_handling']['value']);

//test exception actually does get thrown when exception_handling is set to 'throw'
try {
    $foo->set(array(
        'boo'=> 'baz'
    ));
} catch (Exception $e) {
    assert(false !== strpos($e->getMessage(), 'invalid setting name: <i>boo</i>'));
}

//test setting invalid attribute name using set()
// $foo->set(array(
// 'request_client' => (object) array('boo'=>'baz')
// ));
// assert(false);

//test setting invalid attribute value using set()
// $foo->set(array(
// 'request_client' => array('boo'=>'baz')
// ));
// assert(false);

//test chaining set()
$foo->set(array(
    'request_client' => (object) array('boo'=>'baz')
))->set(array(
    'exception_handling'=> 'print'
));
assert('print' == $foo->class_settings['exception_handling']['value']);
assert('baz' == $foo->class_settings['request_client']['value']->boo);

//test key validation
$foo = new OauthPanda(array(
    'exception_handling' => 'throw',
    'request_client' => new YahooCurlWrapper,
    'oauth_client' => new StandardOauthWrapper,
    'consumer_key' => YAHOO_OAUTH_CONSUMER_KEY,
    'consumer_secret' => YAHOO_OAUTH_CONSUMER_SECRET
));

//test bad key type
try {
    $foo->set(array('consumer_key'=>array()))->GET(array(
        'url' => 'https://api.login.yahoo.com/oauth/v2/get_request_token'
    ));
    assert(false);
} catch(Exception $e) {
    assert(false !== strpos($e->getMessage(), '<i>consumer_key</i> must be a string not: array'));
}

//test secret validation
$foo = new OauthPanda(array(
    'exception_handling' => 'throw',
    'request_client' => new YahooCurlWrapper,
    'oauth_client' => new StandardOauthWrapper,
    'consumer_key' => YAHOO_OAUTH_CONSUMER_KEY,
    'consumer_secret' => YAHOO_OAUTH_CONSUMER_SECRET
));

//test oauth client w/ bad secret type --> fail
try {
    $foo->set(array('consumer_secret'=>array()))->GET(array(
        'url' => 'https://api.login.yahoo.com/oauth/v2/get_request_token'
    ));
    assert(false);
} catch(Exception $e) {
    assert(false !== strpos($e->getMessage(), '<i>consumer_secret</i> must be a string not: array'));
}

//test called method name validation w/ incorrect method name --> fail
// $foo->BAR(array(
//  'url' => 'blah',
// 'key' => 'qwe123'
// ));

//test called method name validation w/ deliberate method name --> pass
$foo->set(array('request_client' => new YahooCurlWrapper))->BAR(array(
    'request_method' => 'GET',
    'url' => 'https://api.login.yahoo.com/oauth/v2/get_request_token',
));

// test method input name validation w/ invalid value
// $foo->GET(array());

//test correct method input name validation w/ valid value
$foo->GET(array(
    // 'url' => 'https://api.login.yahoo.com/oauth/v2/get_request_token',
    'url' => 'https://api.login.yahoo.com/oauth/v2/get_request_token',
    'params' => array('oauth_callback' => OAUTH_CALLBACK_URL)
));

//BEGIN: standard oauth client tests
//test oauth client w/ no scheme in url --> fail
// $foo->GET(array(
//     // 'url' => 'https://api.login.yahoo.com/oauth/v2/get_request_token',
//     'url' => 'localhost/~eldridge/gist-246976/provider.php',
//     'params' => array('oauth_callback' => OAUTH_CALLBACK_URL)
// ));

//test oauth client w/ non-(http|https) scheme --> pass
$foo->GET(array(
    'url' => 'zzz://localhost/~eldridge/gist-246976/provider.php',
    'params' => array('oauth_callback' => OAUTH_CALLBACK_URL)
));

//test oauth client w/ bad url --> fail
// $foo->GET(array(
//     'url' => 'http//ovider.php'
// ));

//test correct response from yahoo
$foo = new OauthPanda(array(
    'exception_handling' => 'throw',
    'request_client' => new YahooCurlWrapper,
    'oauth_client' => new StandardOauthWrapper,
    'consumer_key' => YAHOO_OAUTH_CONSUMER_KEY,
    'consumer_secret' => YAHOO_OAUTH_CONSUMER_SECRET
));
$response = $foo->GET(array(
    'url' => 'https://api.login.yahoo.com/oauth/v2/get_request_token',
    'params' => array('oauth_callback' => OAUTH_CALLBACK_URL)
));
assert(false !== strpos($response['response_body'], 'oauth_token='));


printf('<pre>%s</pre>', print_r($response, true));

//test correct method input value validation
// $foo->BAR(array(
//  'url' => array()
// ));

?>