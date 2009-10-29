<?php

if (!class_exists('Curl'))) {
    throw new Exception('Curl class (http://github.com/shuber/curl) required');
}

class NetDB {    
    private $curl = new Curl;
    function __construct($uid, $secret){
        $hash = sha1($secret.$uid);
        $this->url = sprintf('http://test.erikeldridge.com/netdb/%s/%s/', $uid, $hash);
    }
    function get($key){
        $url = $this->url.'/'.$key;        
        $response = $this->curl->get($url)->body;
        return json_decode($response);
    }
    function set($key, $value){
        if(!is_string($value)){
           throw(new Exception('value must be a string, not: '.print_r($value, true)));
        }
        $url = $this->url.'/'.$key;
        $params = array('value'=>$value);
        $response = $this->curl->post($url, $params)->body;
        return json_decode($response);
    }
}
?>