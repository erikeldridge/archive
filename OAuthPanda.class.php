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
 
class OAuthPanda
{
    public function __construct($key, $secret, $callback_url=null)
    {
        $this->consumer = new OAuthConsumer(
            $key, 
            $secret,
            $callback_url
        );
        
        //defaults
        $this->signature_method = new OAuthSignatureMethod_HMAC_SHA1();
        $this->token = null;
        $this->headers = array();
        $this->post_params = array();
        $this->get_params = array();
        $this->content = null;
        $this->oauth_param_location = 'header';
        $this->curl_options = array();
    }
    
    //convenient, chainable setter
    public function set($name, $value)
    {        
        $this->$name = $value;
        return $this;
    }
    
    //convenient, method-oriented caller
    public function __call($name, $args)
    {
        $method = strtoupper($name);
        return $this->request($method, $args[0], $args[1], $args[2]);
    }
    
    private function request($request_method, $url, $params=array(), $content=null)
    {
        $request = OAuthRequest::from_consumer_and_token(
            $this->consumer, 
            $this->token, 
            $request_method, 
            $url,
            $params);
            
        $request->sign_request(
            $this->signature_method, 
            $this->consumer, 
            $this->token
        );
        
        switch($this->oauth_param_location){
            case 'url':
                $url = $request->to_url();
                break;
            case 'header':
                $this->headers[] = $request->to_header();
                break;
            case 'post':
                $this->post_params = $request->to_postdata();
                break;
            default:
                throw(new Exception('invalid oauth param location: '.$this->oauth_param_location));
                break;
        }
        
        $response = YahooCurl::fetch($url, $this->post_params, $this->headers, $request->get_normalized_http_method(), $this->content, $this->curl_options);
        
        return $response;
    }
}
?>
