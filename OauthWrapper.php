<?php

interface OauthWrapper
{
    //not static because we can't call static methods from objects assigned to arrays in php 5.2
    //use multi-var params & type hinting because we're not interfacing w/ user
    function sign($consumer_key, $consumer_secret, $url, Array $params, $request_method, $token, $oauth_signature_method);
}

class StandardOauthWrapper implements OauthWrapper
{
    function __construct($include_path=false)
    {
        $this->include_path = $include_path;
    }
    
    //use hard enforcement because we're not interfacing w/ users
    function sign($consumer_key, $consumer_secret, $url, Array $params, $request_method, $token, $oauth_signature_method)
    {
        // check for file when sign() is called so oauthpanda can catch the exception
        if (false === $this->include_path || false === is_file($this->include_path)) {
            $message = sprintf('<p>The standard OAuth client library is required.<br/>'
            .'You can get it here:'
            .'<i><a href="http://oauth.googlecode.com/svn/code/php/OAuth.php">'
            .'http://oauth.googlecode.com/svn/code/php/OAuth.php'
            .'</a></i>.<br/>'
            .'Pass the location of the file into the %s constructor, e.g.,<br/>'
            .'<i>\'oauth_client\' => new %s(\'path/to/OAuth.php\'),</i><br/>', 
            __CLASS__,
            __CLASS__);
            throw new Exception($message);
        } 
        
        require_once $this->include_path;
        
        //terse enforcement of types because we're not interfacing w/ user
        assert(is_string($consumer_key));
        assert(is_string($consumer_secret));
        assert(is_string($url));
        assert(is_string($request_method));
        assert(is_null($token) || is_object($token));
        assert(is_string($oauth_signature_method));
        
        //validate input according to client particulars
        $parsed_url = parse_url($url);
        if (false === isset($parsed_url['scheme']) || false === isset($parsed_url['host'])) {
            $message = 'A valid url of the form <i>{scheme}://{host}</i> is required, e.g., <i>http://example.com</i>.  Original input: '.$url;
            throw new Exception($message);
        }
        
        $consumer = new OAuthConsumer(
            $consumer_key,
            $consumer_secret
        );
        
        $request = OAuthRequest::from_consumer_and_token(
            $consumer, 
            $token, 
            $request_method, 
            $url,
            $params
        );
        
        switch($oauth_signature_method){
            case 'hmac':
                $oauth_signature_method_obj = new OAuthSignatureMethod_HMAC_SHA1();
                break;
            case 'text':
                $oauth_signature_method_obj = new OAuthSignatureMethod_PLAINTEXT();
                break;
            default:
                throw(new Exception());
        }
        
        $request->sign_request(
            $oauth_signature_method_obj,
            $consumer, 
            $token
        );
        
        // parse_str($request->to_postdata(), $signed_params);
        
        return $request->to_postdata();
    }
}

?>