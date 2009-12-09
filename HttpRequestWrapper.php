<?php

interface HttpRequestWrapper
{
    //use multi-var params & type hinting because we're not interfacing w/ user
    function request($request_method, Array $headers, $url, $param_string);
}

class YahooCurlWrapper implements HttpRequestWrapper
{       
    function __construct($include_path=false)
    {
        $this->include_path = $include_path;
    }
    
    function request($request_method, Array $headers, $url, $param_string)
    {
        //check for dependencies when request() is called so oauthpanda can catch the exception
        if (false === $this->include_path || false === is_file($this->include_path)) {
            $message = sprintf('<p>The <i>YahooCurl</i> client library is required. You can get it here:<br/>'
                .'<i><a href="http://github.com/yahoo/yos-social-php5/blob/master/lib/Yahoo/YahooCurl.class.php">'
                .'http://github.com/yahoo/yos-social-php5/blob/master/lib/Yahoo/YahooCurl.class.php'
                .'</a></i><br/>'
                .'Pass the location of the base file into the %s constructor, e.g.,<br/>'
                .'<i>\'oauth_client\' => new %s(\'path/to/YahooCurl.class.php\'),</i><br/>', 
                __CLASS__,
                __CLASS__);
            throw new Exception($message);
        }
        
        require_once $this->include_path;
        
        //terse enforcement of types because we're not interfacing w/ user
        assert(is_string($url));
        assert(is_string($param_string) || is_null($param_string));
        
        //for now, only support get and post
        assert(is_string($request_method) && in_array($request_method, array('GET', 'POST')));
        
        switch ($request_method) {
            case 'GET':
            
                //append params to url, so they aren't encoded in a non-oauth-compliant way inside lib
                $url .= '?'.$param_string;
                
                $post_params = null;
                break;
            case 'POST':
                $post_params = $param_string;
                break;
            default:
                break;
        }
        //http://github.com/yahoo/yos-social-php5/blob/master/lib/Yahoo/YahooCurl.class.php
        require_once 'YahooCurl.class.php';
        
        $http = YahooCurl::fetch(
            $url, 
            null,            
            $headers, 
            $request_method, 
            $post_params
        );
        
        return $http;
    }
}

class ShuberCurlWrapper implements HttpRequestWrapper
{
    function __construct($include_path=false)
    {
        $this->include_path = $include_path;
    }
    
    function request($request_method, Array $headers, $url, $param_string)
    {
        // check for dependencies when request() is called so oauthpanda can catch the exception
        if (false === $this->include_path || false === is_file($this->include_path)) {
            $message = '<p>Shuber\'s <i>Curl</i> client library is required. You can get it here:<br/>'
                .'<i><a href="http://github.com/shuber/curl">'
                .'http://github.com/shuber/curl'
                .'</a></i><br/>'
                .'Pass the location of the base file into the ShuberCurlWrapper constructor, e.g.,<br/>'
                .'<i>\'request_client\' => new ShuberCurlWrapper(\'../../curl/curl.php\'),</i><br/>';
            throw new Exception($message);
        }
    
        require_once $this->include_path;
    
        //terse enforcement of types because we're not interfacing w/ user
        assert(is_string($url));
        assert(is_string($param_string) || is_null($param_string));
    
        //for now, only support get and post
        assert(is_string($request_method) && in_array($request_method, array('GET', 'POST')));
    
        $curl = new Curl;
        
        switch ($request_method) {
            case 'GET':
        
                //append params to url, so they aren't encoded in a non-oauth-compliant way inside lib
                $url .= '?'.$param_string;
                
                $response = $curl->get($url);
                
                break;
            case 'POST':
            
                // reformat param pairs back into assoc array
                $param_pairs = explode('&', $param_string);
                $params = array();
                foreach ($param_pairs as $pair) {
                    list($name, $value) = explode('=', $pair);
                    $params[$name] = $value;
                }
                
                $response = $curl->post($url, $params);
                break;
            default:
                break;
        }
    
        return $response;
    }
}

?>