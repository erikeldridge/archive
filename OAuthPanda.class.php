<?php

/**
 * OAuthPanda
 *
 * Find OAuth documentation and support on Yahoo! Developer Network: http://developer.yahoo.com/oauth
 *
 * @package    http://github.com/erikeldridge/oauthpanda
 * @author     Erik Eldridge
 * @copyright  Copyrights for code authored by Erik Eldridge is licensed under the following terms:
 * @license    BSD Open Source License
 *
 *   Permission is hereby granted, free of charge, to any person obtaining a copy
 *   of this software and associated documentation files (the "Software"), to deal
 *   in the Software without restriction, including without limitation the rights
 *   to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 *   copies of the Software, and to permit persons to whom the Software is
 *   furnished to do so, subject to the following conditions:
 *
 *   The above copyright notice and this permission notice shall be included in
 *   all copies or substantial portions of the Software.
 *
 *   THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 *   IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 *   FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 *   AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 *   LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 *   OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 *   THE SOFTWARE.
 **/

class OauthPandaResponse {
    function __construct($args)
    {
        if(is_array($args)){
            foreach($args as $key => $value){
                $this->$key = $value;
            }
        }
    }
    function __toString()
    {
        return sprintf('<hr/><pre>%s</pre><hr/>', print_r($this, true));
    }
}

interface OauthPandaRequest {
    function request($request_method, $url, Array $headers, $post_params);
}

class OauthPandaYahooCurlRequest implements OauthPandaRequest {
    function request($request_method, $url, Array $headers, $post_params)
    {
        //signature: fetch($url, Array $params, $headers = array(), $method = self::GET, $post = null, $options = array())
        $http = YahooCurl::fetch($url, null, $headers, $request_method, $post_params);
        
        return new OauthPandaResponse($http);
    }
}

class OAuthPanda
{
    //define bare minimum requirements (key, secret) and defaults
    public function __construct($key, $secret)
    {
        $this->consumer = new OAuthConsumer(
            $key, 
            $secret
        );
        
        //defaults
        $this->request_client = new OauthPandaYahooCurlRequest;
        $this->signature_method = new OAuthSignatureMethod_HMAC_SHA1();
        $this->token = null;
        $this->headers = array();
        $this->post_params = '';
        $this->oauth_param_location = 'header';
    }
    
    //convenient, chainable method for custom settings
    public function set(Array $args)
    {        
        foreach($args as $key => $value){
            $this->$key = $value;
        }
        return $this;
    }
    
    //convenient, method-oriented caller
    public function __call($name, $args)
    {
        //format/validate input
        $request_method = strtoupper($name);
        $this->url = $args[0];
        $extra_params = $args[1];
        
        //BEGIN: oauth config
        $request = OAuthRequest::from_consumer_and_token(
            $this->consumer, 
            $this->token, 
            $request_method, 
            $this->url,
            $extra_params);
            
        $request->sign_request(
            $this->signature_method, 
            $this->consumer, 
            $this->token
        );

        switch($this->oauth_param_location){
            case 'url':
                $this->url = $request->to_url();
                break;
            case 'header':
                $this->headers[] = $request->to_header();        
                break;
            case 'post':
                $this->post_params = $request->to_postdata();
                break;
            default:
                $message = "Invalid oauth param location ($this->oauth_param_location).  "
                    ."Valid options are 'url', 'header' (default), or 'post'.";
                throw(new Exception($message));
                break;
        }
        
        $this->request_method = $request->get_normalized_http_method();
        //END: oauth config
        
        //make request
        $response = $this->request_client->request(
            $this->request_method, 
            $this->url, 
            $this->headers,
            $this->post_params
        );
        
        return $response;
    }
}
?>
