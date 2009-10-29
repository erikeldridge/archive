<?php

if (!class_exists('Curl'))) {
    throw new Exception('Curl class (http://github.com/shuber/curl) required');
}

class NetDB {
    
    //template api url
    private $url = "http://test.erikeldridge.com/netdb/{uid}/{hash}/{key}";
    
    //str_replace search args (using str_replace because it's self-descriptive)
    private $search = array('{uid}', '{hash}', '{key}');
    
    private $curl = new Curl;
    function __construct($uid, $secret){
        $this->uid = $uid;
        $this->secret = $secret;
        $this->hash = sha1($secret.$uid);
    }
    function get($key){
        
        //prep url
        $replace = array($this->uid, $this->hash, $key);
        $url = str_replace($this->search, $replace, $this->url);
        
        $response = $this->curl->get($url)->body;
        return json_decode($response);
    }
    function set($key, $value){
        if(!is_string($value)){
           throw(new Exception('value must be a string, not: '.print_r($value, true)));
        }
        
        //prep url
        $replace = array($this->uid, $this->hash, $key);
        $url = str_replace($this->search, $replace, $this->url);
        
        $params = array('value'=>$value);
        $response = $this->curl->post($url, $params)->body;
        return json_decode($response);
    }
}
?>