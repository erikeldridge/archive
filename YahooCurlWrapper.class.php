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
 
$request_class_file_name = 'YahooCurl.class.php';
$request_class_repository_url = 'http://github.com/yahoo/yos-social-php5/blob/master/lib/Yahoo/YahooCurl.class.php';
if (is_file($request_class_file_name)) {
	require_once $request_class_file_name;
} else {
	$message = sprintf(
		'<ul>'
			.'<li>File not found: <i>%s</i></li>'
			.'<li>Include path: %s.</li>'
			.'<li>%s can be obtained from <a href="%s">%s</a></li>'
		.'</ul>', 
		$request_class_file_name,
		get_include_path(),
		$request_class_file_name,
		$request_class_repository_url,
		$request_class_repository_url
	);
	trigger_error($message, E_USER_ERROR);
}

require_once 'HttpRequestWrapper.interface.php';

class YahooCurlWrapper implements RequestWrapper {
	
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
	
    function request($input=array())
    {
        
        $settings = array(
            'request_method' => array(
                'required' => true,
				'validate' => create_function('$value', 
					'return is_string($value) && in_array($value, array("GET", "POST", "PUT", "DELETE"));'
				),
            ),
            'headers' => array(
                'required' => false,
                'value' => array(),
				'validate' => create_function('$value', 
					'return is_array($value);'
				),
            ),
	        'url' => array(
	            'required' => true,
				'validate' => create_function('$value', 
					'return is_string($value);'
				),
	        ),
	        'post_params' => array(
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
	        )
        );
        
	    if(false === isset($input)){
    
	        //display requirements, options, usage, & examples
	        $exception = new Exception('input required');
			self::handleException($settings, $exception);
	    }

	    //apply arguments
	    foreach($input as $arg_name => $arg_value){
    
	        //validate name
	        if(false === array_key_exists($arg_name, $settings)){
				$message = sprintf("invalid name: '%s'", $arg_name);
	            $exception = new Exception($message);
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
	        if($setting[$key]['required'] && !isset($setting[$key]['value'])){
				$message = sprintf('%s is required', $key);
				$exception = new Exception($message);
				self::handleException($settings, $exception);
	        }
	    }
        
        //YahooCurl::fetch($url, Array $params, $headers = array(), $method = self::GET, $post = null, $options = array())
        $http = YahooCurl::fetch(
            $settings['url'], 
            
            //Get params are assumed to be in url
            null, 
            
            $settings['headers'], 
            $settings['request_method'], 
            $settings['post_params']
        );
        
        return $http;
    }
}
?>