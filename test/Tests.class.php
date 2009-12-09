<?php

require '../private.php';

function createStandardPandaObj()
{
    $panda = new OauthPanda(array(
        'exception_handling' => 'throw',
        'request_client' => new YahooCurlWrapper('../YahooCurl.class.php'),
        'oauth_client' => new StandardOauthWrapper('../OAuth.php'),
        'consumer_key' => YAHOO_OAUTH_CONSUMER_KEY,
        'consumer_secret' => YAHOO_OAUTH_CONSUMER_SECRET
    ));
    
    return $panda;
}

class TestUtils
{
    static function respond($test_name, $result, $message=null)
    {
        assert(is_string($test_name));
        assert(is_string($result));
        assert(is_string($message) || is_null($message));
        
        $response = (object) array(
            'result' => $result,
            'test_name' => $test_name
        );
        
        if (true === is_string($message)) {
            $response->message = $message;
        }
        
        echo json_encode($response);
    }
    
    static function assertTrue($value, $message=null)
    {
        assert(is_bool($value));
        assert(is_string($message) || is_null($message));
        
        //determine test via stack to avoid unintuitively requiring test name be passed as arg to this fn
        $backtrace = debug_backtrace();
        $first_trace_item = array_pop($backtrace);

        if (true === $value) {
            self::respond($first_trace_item['function'], 'pass', $message);
        } else {
            self::respond($first_trace_item['function'], 'fail', $message);
        }
    }
}

class Tests
{
    static function test1()
    {
        require '../OauthPanda.class.php';
        $foo = new OauthPanda(array(
            'request_client' => new stdclass,
            'oauth_client' => new StandardOauthWrapper,
            'consumer_key' => YAHOO_OAUTH_CONSUMER_KEY,
            'consumer_secret' => YAHOO_OAUTH_CONSUMER_SECRET
        ));
        TestUtils::assertTrue(
            'print' == $foo->class_settings['exception_handling']['value'],
            'testing default setting value'
        );
    }
    
    static function test2()
    {
        require '../OauthPanda.class.php';
        $foo = new OauthPanda(array(
            'exception_handling' => 'throw',
            'request_client' => new YahooCurlWrapper,
            'oauth_client' => new StandardOauthWrapper,
            'consumer_key' => YAHOO_OAUTH_CONSUMER_KEY,
            'consumer_secret' => YAHOO_OAUTH_CONSUMER_SECRET
        ));
        TestUtils::assertTrue(
            'throw' == $foo->class_settings['exception_handling']['value'],
            'testing custom setting value'
        );
    }
    
    static function test3()
    {
        require '../OauthPanda.class.php';
        
        $foo = new OauthPanda(array(
            'exception_handling' => 'throw',
            'request_client' => new YahooCurlWrapper,
            'oauth_client' => new StandardOauthWrapper,
            'consumer_key' => YAHOO_OAUTH_CONSUMER_KEY,
            'consumer_secret' => YAHOO_OAUTH_CONSUMER_SECRET
        ));
        
        try {
            $foo->set(array(
                'boo'=> 'baz'
            ));
        } catch (Exception $e) {
            TestUtils::assertTrue(
                false !== strpos($e->getMessage(), 'invalid setting name: <i>boo</i>'),
                'passing invalid setting name to set()'
            );
        }

    }
    
    static function test4()
    {
        require '../OauthPanda.class.php';
        
        $foo = new OauthPanda(array(
            'exception_handling' => 'throw',
            'request_client' => new YahooCurlWrapper,
            'oauth_client' => new StandardOauthWrapper,
            'consumer_key' => YAHOO_OAUTH_CONSUMER_KEY,
            'consumer_secret' => YAHOO_OAUTH_CONSUMER_SECRET
        ));
        
        try {
            $foo->set(array(
                'request_client' => array('boo'=>'baz')
            ));
            TestUtils::assertTrue(false, 'passing invalid setting value to set() should throw');
        } catch (Exception $e) {
            TestUtils::assertTrue(
                false !== strpos($e->getMessage(), '<i>request_client</i> must be an object, not: array')
            );
        }
    }
    
    static function test5()
    {
        require '../OauthPanda.class.php';
        
        $foo = new OauthPanda(array(
            'exception_handling' => 'throw',
            'request_client' => new YahooCurlWrapper,
            'oauth_client' => new StandardOauthWrapper,
            'consumer_key' => YAHOO_OAUTH_CONSUMER_KEY,
            'consumer_secret' => YAHOO_OAUTH_CONSUMER_SECRET
        ));
        
        try {
            $foo->set(array(
                'request_client' => (object) array('boo'=>'baz')
            ))->set(array(
                'exception_handling'=> 'print'
            ));
            TestUtils::respond(__FUNCTION__, 'pass');
        } catch (Exception $e) {
            TestUtils::assertTrue(false, 'chaining set () shouldn\'t throw exception');
        }
    }
    
    static function test6()
    {
        require '../OauthPanda.class.php';
        
        $foo = new OauthPanda(array(
            'exception_handling' => 'throw',
            'request_client' => new YahooCurlWrapper,
            'oauth_client' => new StandardOauthWrapper,
            'consumer_key' => YAHOO_OAUTH_CONSUMER_KEY,
            'consumer_secret' => YAHOO_OAUTH_CONSUMER_SECRET
        ));
        
        try {
            $foo->set(array(
                // 'boo'=> 'baz'
            ));
            TestUtils::respond(__FUNCTION__, 'pass');
        } catch (Exception $e) {
            TestUtils::assertTrue(false, 'setting empty array shouldn\'t throw exception');
        }
    }
    
    //test input callbacks
    static function test7()
    {
        require '../OauthPanda.class.php';
        
        $foo = new OauthPanda(array(
            'exception_handling' => 'throw',
            'request_client' => new YahooCurlWrapper,
            'oauth_client' => new StandardOauthWrapper,
            'consumer_key' => YAHOO_OAUTH_CONSUMER_KEY,
            'consumer_secret' => YAHOO_OAUTH_CONSUMER_SECRET
        ));
        
        try {
            $foo->set(array('consumer_key'=>array()))->GET(array(
                'url' => 'https://api.login.yahoo.com/oauth/v2/get_request_token'
            ));
            TestUtils::assertTrue(false, 'bad consumer key type should throw exception, ie we shouldn\'t get here');
        } catch (Exception $e) {
            TestUtils::assertTrue(
                false !== strpos($e->getMessage(), '<i>consumer_key</i> must be a string not: array'),
                'bad consumer key type should throw exception'
            );
        }
    }
    
    static function test8()
    {
        require '../OauthPanda.class.php';
        
        $foo = new OauthPanda(array(
            'exception_handling' => 'throw',
            'request_client' => new YahooCurlWrapper,
            'oauth_client' => new StandardOauthWrapper,
            'consumer_key' => YAHOO_OAUTH_CONSUMER_KEY,
            'consumer_secret' => YAHOO_OAUTH_CONSUMER_SECRET
        ));
        
        try {
            $foo->set(array('consumer_secret'=>array()))->GET(array(
                'url' => 'https://api.login.yahoo.com/oauth/v2/get_request_token'
            ));
            TestUtils::assertTrue(false, 'bad consumer secret type should throw exception, ie we shouldn\'t get here');
        } catch (Exception $e) {
            TestUtils::assertTrue(
                false !== strpos($e->getMessage(), '<i>consumer_secret</i> must be a string not: array')
            );
        }
    }
    
    static function test9()
    {
        require '../OauthPanda.class.php';
        
        $foo = new OauthPanda(array(
            'exception_handling' => 'throw',
            'request_client' => new YahooCurlWrapper,
            'oauth_client' => new StandardOauthWrapper('../OAuth.php'),
            'consumer_key' => YAHOO_OAUTH_CONSUMER_KEY,
            'consumer_secret' => YAHOO_OAUTH_CONSUMER_SECRET
        ));
        
        try {
            
            //calling BAR() would throw error, but deliberately setting method overrides
            $foo->set(array('request_client' => new YahooCurlWrapper('../YahooCurl.class.php')))->BAR(array(
                'request_method' => 'GET',
                'url' => 'https://api.login.yahoo.com/oauth/v2/get_request_token',
            ));
            
            TestUtils::respond(__FUNCTION__, 'pass');
        } catch (Exception $e) {
            TestUtils::assertTrue(
                false,
                'deliberately setting request method should not throw exception<p/>'.print_r($e, true)
            );
        }
    }
    
    static function test10()
    {
        require '../OauthPanda.class.php';
        
        $foo = new OauthPanda(array(
            'exception_handling' => 'throw',
            'request_client' => new YahooCurlWrapper('../YahooCurl.class.php'),
            'oauth_client' => new StandardOauthWrapper('../OAuth.php'),
            'consumer_key' => YAHOO_OAUTH_CONSUMER_KEY,
            'consumer_secret' => YAHOO_OAUTH_CONSUMER_SECRET
        ));
        
        try {
            $foo->BAR(array(
                'url' => 'https://api.login.yahoo.com/oauth/v2/get_request_token',
            ));
            TestUtils::assertTrue(
                false,
                'invalid request method should throw exception'
            );
        } catch (Exception $e) {
            TestUtils::assertTrue(
                false !== strpos($e->getMessage(), '<i>request_method</i> must be "GET" or "POST", not "BAR"')
                // print_r($e, true)
            );
        }
    }
    
    // test method input name validation w/ invalid value
    static function test11()
    {
        require '../OauthPanda.class.php';
        
        $foo = new OauthPanda(array(
            'exception_handling' => 'throw',
            'request_client' => new YahooCurlWrapper('../YahooCurl.class.php'),
            'oauth_client' => new StandardOauthWrapper('../OAuth.php'),
            'consumer_key' => YAHOO_OAUTH_CONSUMER_KEY,
            'consumer_secret' => YAHOO_OAUTH_CONSUMER_SECRET
        ));
        
        try {
            $foo->GET(array());
            TestUtils::assertTrue(
                false,
                'request w/ empty array should throw exception'
            );
        } catch (Exception $e) {
            TestUtils::assertTrue(
                false !== strpos($e->getMessage(), '<i>url</i> is required')
            );
        }
    }
    
    //BEGIN: standard oauth client tests
    
    //test oauth client w/ no scheme in url --> fail
    static function test12()
    {
        require '../OauthPanda.class.php';
        
        $foo = createStandardPandaObj();
        
        try {
            $foo->GET(array(
                'url' => 'example.com.php'
            ));
            TestUtils::assertTrue(
                false,
                'should throw exception'
            );
        } catch (Exception $e) {
            TestUtils::assertTrue(
                false !== strpos($e->getMessage(), 'A valid url of the form <i>{scheme}://{host}</i> is required')
            );
        }
    }
    
    //test oauth client w/ non-(http|https) scheme --> pass
    static function test13()
    {
        require '../OauthPanda.class.php';
        
        $foo = createStandardPandaObj();
        
        try {
            $foo->GET(array(
                'url' => 'zzz://example.com.php',
                'params' => array('oauth_callback' => OAUTH_CALLBACK_URL)
            ));
            
            //shouldn't throw exception
            TestUtils::assertTrue(true);
            
        } catch (Exception $e) {
            TestUtils::assertTrue(
                false,
                'if we\'re here, there\'s a problem '.print_r($e, true)
            );
        }
    }
    
    // test oauth client w/ bad url --> fail
    static function test14()
    {
        require '../OauthPanda.class.php';
        
        $foo = createStandardPandaObj();
        
        try {
            $foo->GET(array(
                'url' => 'http//ovider.php'
            ));
            
            //should throw exception
            TestUtils::assertTrue(false);
            
        } catch (Exception $e) {
            TestUtils::assertTrue(
                
                //parse_url can't determine scheme-host split, so same error is thrown as for no scheme
                false !== strpos($e->getMessage(), 'A valid url of the form <i>{scheme}://{host}</i> is required')
                
            );
        }
    }
    
    // BEGIN: yahoo! provider tests
    
    // test correct response from yahoo
    static function test15()
    {
        require '../OauthPanda.class.php';
        
        $foo = new OauthPanda(array(
            'exception_handling' => 'throw',
            'request_client' => new YahooCurlWrapper('../YahooCurl.class.php'),
            'oauth_client' => new StandardOauthWrapper('../OAuth.php'),
            'consumer_key' => YAHOO_OAUTH_CONSUMER_KEY,
            'consumer_secret' => YAHOO_OAUTH_CONSUMER_SECRET
        ));
        
        try {
            $response = $foo->GET(array(
                'url' => 'https://api.login.yahoo.com/oauth/v2/get_request_token',
                'params' => array('oauth_callback' => OAUTH_CALLBACK_URL)
            ));
            
            //we should get a request token back
            TestUtils::assertTrue(
                false !== strpos($response['response_body'], 'oauth_token=')
            );
            
        } catch (Exception $e) {
            
            //no exception should be thrown
            TestUtils::assertTrue(
                false,
                ''.print_r($e, true)
            );
        }
    }
}

?>