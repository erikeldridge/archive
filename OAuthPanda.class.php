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

interface OauthClientPanda {
    public function sign($consumer_key, $consumer_secret, $url, $params=array(), 
        $request_method='GET', $token=null, $oauth_signature_method='HMAC');
}

class GoogleCodeOauthClientPanda implements OauthClientPanda {
    public function sign(
        $consumer_key, $consumer_secret, $url, $params=array(), 
        $request_method='GET', $token=null, $oauth_signature_method='HMAC')
    {
        $consumer = new OAuthConsumer(
            $consumer_key, 
            $consumer_secret
        );
        
        $request = OAuthRequest::from_consumer_and_token(
            $consumer, 
            $token, 
            $request_method, 
            $url,
            $params);
        
        switch($oauth_signature_method){
            case 'hmac':
                $oauth_signature_method = new OAuthSignatureMethod_HMAC_SHA1();
                break;
            case 'text':
                $oauth_signature_method = new OAuthSignatureMethod_PLAINTEXT();
                break;
            default:
                throw(new Exception());
        }
        
        $request->sign_request(
            $oauth_signature_method,
            $consumer, 
            $token
        );
        
        parse_str($request->to_postdata(), $signed_params);
        
        return $signed_params;
    }
}

interface HttpRequestPanda {
    function request($request_method, $url, Array $headers, $post_params);
}

class YahooCurlPanda implements HttpRequestPanda {
    function request($request_method, $url, Array $headers, $post_params)
    {
        //signature: fetch($url, Array $params, $headers = array(), $method = self::GET, $post = null, $options = array())
        $http = YahooCurl::fetch($url, null, $headers, $request_method, $post_params);
        
        return $http;
    }
}

class OAuthPanda
{  
    public function __call($name, $args)
    {
        $request_method = strtoupper($name);
        
        $settings = array(
            //required
            'consumer_key' => null,
            'consumer_secret' => null,
            'url' => null,
            
            //defaults
            'oauth_client' => new GoogleCodeOauthClientPanda,
            'request_client' => new YahooCurlPanda,
            'oauth_signature_method' => 'hmac',
            'oauth_param_location' => 'header',
            'token' => null,
            'headers' => array(),
            'params' => array()
        );
        
        //apply arguments
        foreach($args[0] as $arg_name => $arg_value){
            if(FALSE === array_key_exists($arg_name, $settings)){
                throw(new Exception(''));
            }
            $settings[$arg_name] = $arg_value;
        }
        
        //oauth config
        //$consumer_key, $consumer_secret, $url, $params=array(), $request_method='GET', $token=null, $oauth_signature_method='HMAC'
        $signed_params = $settings['oauth_client']->sign(
            $settings['consumer_key'],
            $settings['consumer_secret'],
            $settings['url'],
            $settings['params'],
            
            //GET, POST, PUT, DELETE, etc
            $request_method,
            
            $settings['token'],
            $settings['oauth_signature_method']
        );
        
        switch($settings['oauth_param_location']){
            case 'url':
                $settings['url'] .= '?'.http_build_query($signed_params);
                break;
            case 'header':
                $settings['headers'][] = $request->to_header();        
                break;
            case 'post':
                $settings['params'] = http_build_query($signed_params);
                break;
            default:
                $message = "Invalid oauth param location (".$settings['oauth_param_location'].").  "
                    ."Valid options are 'url', 'header' (default), or 'post'.";
                throw(new Exception($message));
                break;
        }
        //END: oauth config
        
        //make request
        $response = $settings['request_client']->request(
            $request_method, 
            $settings['url'], 
            $settings['headers'],
            $settings['params']
        );
        
        return $response;
    }
}
?>
