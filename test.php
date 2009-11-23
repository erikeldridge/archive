<?php

require_once 'private.php';
require_once 'OAuth.php';
require_once 'YahooCurl.class.php';
require_once 'OAuthPanda.class.php';

class TestCase {
    function __construct()
    {
        if(method_exists($this, 'setUp')){
            $this->setUp();
        }
        foreach(get_class_methods(get_class($this)) as $method){
            if(0 === strpos($method, 'test')){
                try{
                    $this->{$method}();
                }catch(Exception $e){
                    self::dumpPretty($e);
                }
            }
        }
        if(method_exists($this, 'tearDown')){
            $this->tearDown();
        }
    }
    
    static function dumpPretty($value)
    {
        printf('<pre>%s</pre><hr/>', print_r($value, true));
    }
    
    function assertEquals($value1, $value2)
    {
       if($value1 != $value2){
           throw(new Exception());
       }
    }

    function assertTrue($value)
    {
        if(FALSE === $value){
            throw(new Exception());
        }
    }
}
class DefaultSettingsTest extends TestCase {
    
    function setUp()
    {        
        //set up
        $this->panda = new OAuthPanda(YAHOO_OAUTH_CONSUMER_KEY, YAHOO_OAUTH_CONSUMER_SECRET);
    }
    
    function testConsumerSettings()
    {
        // dumpPretty($this->panda->consumer);
        $this->assertEquals($this->panda->consumer->key, YAHOO_OAUTH_CONSUMER_KEY);
        $this->assertEquals($this->panda->consumer->secret, YAHOO_OAUTH_CONSUMER_SECRET);
        $this->assertEquals($this->panda->consumer->callback, NULL);
    }
    
    function testSettings()
    {
        $this->assertEquals($this->panda->token, NULL);
        
        $this->assertEquals(is_array($this->panda->headers), TRUE);
        $this->assertEquals(count($this->panda->headers), 0);
        
        $this->assertEquals($this->panda->post_params, '');
        
        $this->assertEquals($this->panda->oauth_param_location, 'header');
    }
}

class CustomSettingsTest extends TestCase {
    function setUp()
    {        
        $this->panda = new OAuthPanda(YAHOO_OAUTH_CONSUMER_KEY, YAHOO_OAUTH_CONSUMER_SECRET);
    }
    function testSingleSet()
    {
        $this->panda->set(array('key' => 'val'));
        $this->assertEquals($this->panda->key, 'val');
    }
    function testMultiSet()
    {
        $this->panda->set(array('key1' => 'val1', 'key2' => 'val2'));
        $this->assertEquals($this->panda->key1, 'val1');
        $this->assertEquals($this->panda->key2, 'val2');
    }
}

class RequestTokenTest extends TestCase {

    function testFetchYahooToken()
    {
        $panda = new OAuthPanda(YAHOO_OAUTH_CONSUMER_KEY, YAHOO_OAUTH_CONSUMER_SECRET);
        $response = $panda->set(array(
                'oauth_param_location' => 'url'
            ))->GET(
                'https://api.login.yahoo.com/oauth/v2/get_request_token', 
                array('oauth_callback' => OAUTH_CALLBACK_URL)
            );
        parse_str($response['response_body'], $data);
        $this->assertTrue(isset($data['oauth_token']));
    }
    
    function testFetchTwitterToken()
    {
        $panda = new OAuthPanda(TWITTER_OAUTH_CONSUMER_KEY, TWITTER_OAUTH_CONSUMER_SECRET);
        $response = $panda->set(array(
                'oauth_param_location' => 'url'
            ))->GET(
                'http://twitter.com/oauth/request_token', 
                array('oauth_callback' => OAUTH_CALLBACK_URL)
            );
        parse_str($response['response_body'], $data);
        $this->assertTrue(isset($data['oauth_token']));
    }
}

$test = new DefaultSettingsTest();
$test = new CustomSettingsTest();
$test = new RequestTokenTest();
?>