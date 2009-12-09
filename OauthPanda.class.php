<?php

require 'OauthWrapper.php';
require 'HttpRequestWrapper.php';

//the standard exception class truncates the message string
class VerboseException extends Exception
{
    function __construct($message)
    {
        $this->message = $message;
    }
}

class OauthPanda
{
    public $class_settings;
    
    function __construct()
    {
        $args = func_get_args();
        
        //class-level settings
        $this->class_settings = array(
            'request_client' => array(
                'required' => 'true',
                'validate' => create_function('$value', 
                    'if(false === is_object($value)){
                        $message = "<i>request_client</i> must be an object, not: ".gettype($value);
                        throw(new Exception($message));
                    }'
                )
              ),
            'oauth_client' => array(
                'required' => 'true',
                'validate' => create_function('$value', 
                'if(false === is_object($value)){
                    $message = "<i>oauth_client</i> must be an object, not: ".gettype($value);
                    throw(new Exception($message));
                }'
                )
            ),
            'consumer_key' => array(
                'required' => 'true',
                'validate' => create_function('$value',
                  'if (false === is_string($value)) {
                      $message = "<i>consumer_key</i> must be a string not: ".gettype($value);
                    }

                    if (isset($message)) {
                        throw (new Exception($message));
                    }'
                ),
            ),
            'consumer_secret' => array(
                'required' => 'true',
                'validate' => create_function('$value',
                  'if (false === is_string($value)) {
                      $message = "<i>consumer_secret</i> must be a string not: ".gettype($value);
                    }

                    if (isset($message)) {
                        throw (new Exception($message));
                    }'
                ),
            ),
            'token' => array(
              'required' => 'false',
              'value' => null,
              'validate' => create_function('$value',
                  'if (false === is_object($value)) {
                      $message = "<i>token</i> must be an object not a(n): ".gettype($value);
                      throw (new Exception($message));
                  }'
              ),
            ),
            'oauth_signature_method' => array(
              'required' => 'false',
              'value' => 'hmac',
              'validate' => create_function('$value',
  
                  '$valid_request_method_names = array("hmac", "plain");
  
                  if (false === is_string($value)){
                      $message = sprintf("<i>oauth_signature_method</i> must be a string not an %s.", gettype($value));
                  } elseif (false === in_array($value, $valid_request_method_names)) {
                      $message = sprintf(
                          "<i>oauth_signature_method</i> must be \"hmac\" or \"plain\", not \"%s\".",
                          $value);
                    }

                    if (isset($message)) {
                        throw (new Exception($message));
                  }'
              ),
            ),
            'oauth_params_location' => array(
              'required' => 'false',
              'value' => 'url',
              'validate' => create_function('$value',

                  '$valid_values = array("header", "url", "post");

                  if (false === is_string($value)){
                      $message = sprintf("<i>oauth_params_location</i> must be a string not an %s.", gettype($value));
                  } elseif (false === in_array($value, $valid_values)) {
                      $message = sprintf(
                          "<i>oauth_params_location</i> must be \"header\", \"url\", or \"post\", not \"%s\".",
                          $value);
                  }
                  
                  if (isset($message)) {
                      throw (new Exception($message));
                }'
              ),
            ),
            'exception_handling' => array(
                'validate' => create_function('$value',
                
                    '$valid_values = array("print", "throw");
                    
                    if (false === is_string($value) 
                        || false === in_array($value, $valid_values)) {
                            $message = "<i>exception_handling</i> must be \'print\' or \'throw\'";
                            throw(new Exception($message));
                    }'
                ),
                'value' => 'print',
                'required' => 'false'
            )
        );
        
        $this->requireInput($args, $this->class_settings);
        
        $this->set($args[0]);
    }
    
    function set()
    {
        $args = func_get_args();

        $this->requireInput($args, $this->class_settings);
        
        $validated = $this->validateInput($args[0], $this->class_settings);
        
        //set validated values
        foreach ($validated as $validated_name => $validated_value) {
            $this->class_settings[$validated_name]['value'] = $validated_value;
        }
        
        $this->enforceRequiredInput($this->class_settings);
        
        //make function chainable
        return $this;
    }
    
    function __call($called_method_name, $args)
    {               
        $function_settings = array(
            'url' => array(
                'required' => 'true',
                'validate' => create_function('$value',
                    'if (false === is_string($value)) {
                        $message = sprintf("<i>url</i> must be a string not an %s.", gettype($value));
                        throw (new Exception($message));
                    }'
                ),
            ),
            'headers' => array(
                'required' => false,
                'value' => array(),
                'validate' => create_function('$value', 
                    'if (false === is_array($value)) {
                        $message = sprintf("<i>headers</i> must be an array not a(n) %s.", gettype($value));
                        throw (new Exception($message));
                    }'
                ),
            ),
            'params' => array(
                'required' => false,
                'value' => array(),
                'validate' => create_function('$value', 
                    'if (false === is_array($value)) {
                        $message = sprintf("<i>params</i> must be an array not a(n) %s.", gettype($value));
                        throw (new Exception($message));
                    }'
                ),
            ),
            'request_method' => array(
                'validate' => create_function('$value',
                
                    '$valid_request_method_names = array("GET", "POST");
                    
                    if (false === is_string($value)){
                        $message = sprintf("<i>request_method</i> must be a string not an %s.", gettype($value));
                    } elseif (false === in_array($value, $valid_request_method_names)) {
                        $message = sprintf(
                            "<i>request_method</i> must be \"GET\" or \"POST\", not \"%s\"."
                            ."  Note: <i>request_method</i> is defined by name of method called, "
                            ."e.g., calling GET() sets <i>request_method</i> to \"GET\"", 
                            $value);
                    }

                    if (isset($message)) {
                        throw (new Exception($message));
                    }'
                ),
                'required' => 'true'
            ),
        );
        
        $this->requireInput($args, $function_settings);
        
        //use deliberately set request method (though still validate it)
        if (false === array_key_exists('request_method', $args[0])) {
            $args[0]['request_method'] = strtoupper($called_method_name);
        }
        
        $validated = $this->validateInput($args[0], $function_settings);
        
        //set validated values
        foreach ($validated as $validated_name => $validated_value) {
            $function_settings[$validated_name]['value'] = $validated_value;
        }
        
        $this->enforceRequiredInput($function_settings);
        
        try {
            $signed_param_string = $this->class_settings['oauth_client']['value']->sign(
                $this->class_settings['consumer_key']['value'],
                $this->class_settings['consumer_secret']['value'],
                $function_settings['url']['value'],
                $function_settings['params']['value'],
                $function_settings['request_method']['value'],            
                $this->class_settings['token']['value'],
                $this->class_settings['oauth_signature_method']['value']
            );
        } catch(Exception $exception) {
            $this->handleException($exception);
        }
        
        switch($this->class_settings['oauth_params_location']['value']){
            case 'url':
                $param_string = $signed_param_string;
                break;
            case 'header':
                $header_string = 'Authorization: OAuth realm=""';
                $params = array();
                $signed_params = explode('&', $signed_param_string);
                
                //differentiate oauth params & build header param str
                foreach ($signed_params as $signed_param_pair) {
                    if (substr($signed_param_pair, 0, 5) == "oauth") {
                        list($param_name, $param_value) = explode('=', $signed_param_pair);
                        $header_string .= sprintf(',%s="%s"', $param_name, $param_value);
                    } else {
                        $params[] = $signed_param_pair;
                    }
                }
                
                $param_string = implode('&', $params);
                
                //add header string to header array 
                $function_settings['headers']['value'][] = $header_string;
                
                break;
            case 'post':
                $param_string = $signed_param_string;
                break;
            default:
                $exception = new Exception('invalid param location');
                $this->handleException($exception);
        }
        
        $response = $this->class_settings['request_client']['value']->request(
            $function_settings['request_method']['value'],
            $function_settings['headers']['value'],
            $function_settings['url']['value'],
            $param_string
        );
 
        return $response;
    }
    
    private function requireInput(Array $args, Array $settings)
    {
        if (count($args) == 0) {
            
            foreach($settings as $setting_name => $setting_value){
                $html = "<i>$setting_name</i>";
                if ('true' == $setting_value['required']) {
                     $html .= ' (required)';
                }
                $settings_list_html[] = $html;
            }
            
            $message = sprintf('<p>input reqd: %s</p>', implode(', ', $settings_list_html));
            
            $exception = new Exception($message);
            
        } elseif (false === is_array($args[0])) {
            $exception = new Exception(sprintf(
                'input must be array, not: %s ', 
                gettype($args[0])
            ));
        } 
        
        if (isset($exception)) {
            $this->handleException($exception);
        }
    }
    
    private function validateInput(Array $input, Array $settings)
    {
        $validated = array();
        
        foreach ($input as $arg_name => $arg_value) {
            
            //validate name
            if (false === array_key_exists($arg_name, $settings)) {
                $exception = new Exception(sprintf(
                    'invalid setting name: <i>%s</i>.  Valid setting names are: <i>%s</i>.',
                    $arg_name,
                    implode(', ', array_keys($settings))
                ));
                $this->handleException($exception);
            }
            
            //validate value
            try {
                $settings[$arg_name]['validate']($arg_value);
            } catch (Exception $exception) {
                $this->handleException($exception);
            }
            
            //assign value
            $validated[$arg_name] = $arg_value;
        }
        
        return $validated;
    }
    
    private function enforceRequiredInput(Array $settings)
    {
        foreach ($settings as $name => $value) {
            if ('true' == $value['required'] && false === isset($value['value'])) {
                $exception = new Exception(sprintf('<i>%s</i> is required', $name));
                $this->handleException($exception);
            }
            
        }
    }
    
    private function handleException(Exception $exception)
    {
        switch($this->class_settings['exception_handling']['value']){
            case 'throw':
                throw($exception);
                break;
            default:
                printf(
                    '<pre>%s</pre>',
                    print_r($exception, true)
                );
                exit;
        }
    }
}

?>