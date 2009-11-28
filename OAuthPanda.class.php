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
	static function handleException($settings, $exception) {
		switch($settings['exception_handling']['value']){
			case 'log':
				error_log(print_r($exception, true));
				break;
			case 'throw':
				throw($exception);
				break;
			default: //'print'
				$message = sprintf(
					'<pre>%s</pre>', 
					print_r($exception, true)
				);
				trigger_error($message, E_USER_ERROR);
				exit;
		}
	}
	
    public function __call($name, $args)
    {        
        $request_method = strtoupper($name);
        $input = $args[0];

        $settings = array(
            'consumer_key' => array(
                'required' => true,
				'validate' => create_function('$value', 
					'return is_string($value);'
				),
            ),
            'consumer_secret' => array(
                'required' => true,
				'validate' => create_function('$value', 
					'return is_string($value);'
				),
            ),
            'url' => array(
                'required' => true,
				'validate' => create_function('$value', 
					'return is_string($value);'
				),
            ),
            'oauth_client' => array(
                'required' => true,
				'validate' => create_function('$value', 
					'if(!is_object($value)){
						$message = "oauth_client must be an object, not: ".print_r($value, true);
						throw(new Exception($message));
					}'
				)
            ),
            'request_client' => array(
                'required' => true,
				'validate' => create_function('$value', 
					'if(!is_object($value)){
						$message = "request_client must be an object, not: ".print_r($value, true);
						throw(new Exception($message));
					}'
				)
	          ),
            'oauth_signature_method' => array(
                'required' => false,
                'value' => 'hmac',
				'validate' => create_function('$value', 
					'return is_string($value) && in_array($value, array("hmac", "plain"));'
				),
            ),
            'oauth_param_location' => array(
                'required' => false,
                'value' => 'header',
				'validate' => create_function('$value', 
					'return is_string($value) && in_array($value, array("header", "post", "url"));'
				),
            ),
            'token' => array(
                'required' => false,
                'value' => null,
				'validate' => create_function('$value',
					'return is_object($value) || is_null($value);'
				),
            ),
            'headers' => array(
                'required' => false,
                'value' => array(),
				'validate' => create_function('$value', 
					'return is_array($value);'
				),
            ),
            'params' => array(
                'required' => false,
                'value' => array(),
				'validate' => create_function('$value', 
					'return is_array($value);'
				),
            ),
	        'exception_handling' => array(
	            'required' => false,
	            'value' => 'print',
				'validate' => create_function('$value', 
					'return is_string($value) && in_array($value, array("print", "log", "throw"));'
				),
	        ),
        );

        if(false === isset($input)){
            
            //display requirements, options, usage, & examples
            throw(new Exception('args required'));
        }
    
        //apply arguments
        foreach($input as $arg_name => $arg_value){
            
            //validate name
            if(false === array_key_exists($arg_name, $settings)){
                $exception = new Exception('invalid name');
				self::handleException($settings, $exception);
            }
            
			//validate value
			try {
				$settings[$arg_name]['validate']($arg_value);
			} catch(Exception $exception) {
				self::handleException($settings, $exception);
			}
			
            $settings[$arg_name]['value'] = $arg_value;
        }
		
		//enforce required input
        foreach($settings as $key => $setting){
            if($setting['required'] && !isset($setting['value'])){
				$message = sprintf('%s is required', $key);
				$exception = new Exception($message);
				self::handleException($settings, $exception);
            }
        }

        //BEGIN: oauth config
        $signed_params = $settings['oauth_client']['value']->sign(array(
            'consumer_key' => $settings['consumer_key'],
            'consumer_secret' => $settings['consumer_secret'],
            'url' => $settings['url']['value'],
            'params' => $settings['params']['value'],
            
            //GET, POST, PUT, DELETE, etc
            'request_method' => $request_method,
            
            'token' => $settings['token']['value'],
            'oauth_signature_method' => $settings['oauth_signature_method']['value']
        ));
        
        switch($settings['oauth_param_location']['value']){
            case 'url':
                $settings['url']['value'] .= '?'.http_build_query($signed_params);
                break;
            case 'header':
                $settings['headers']['value'][] = $request->to_header();        
                break;
            case 'post':
                $settings['params']['value'] = http_build_query($signed_params);
                break;
            default:
                throw(new Exception('invalid param location'));
                break;
        }
        //END: oauth config
        
        //make request
        $response = $settings['request_client']['value']->request(array(
            'request_method' => $request_method,
            'headers' => $settings['headers']['value'],
            'url' => $settings['url']['value'],
            'post_params' => $settings['params']['value']
        ));
        
        return $response;
    }
}
?>
