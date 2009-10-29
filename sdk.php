<?php
class NetDB {
    var $url = "http://test.erikeldridge.com/netdb/{uid}/{hash}/{key}";
    var $search = array('{uid}', '{hash}', '{key}');
    function __construct($uid, $secret){
        $this->uid = $uid;
        $this->secret = $secret;
        $this->hash = sha1($secret.$uid);
    }
    function get($key){
        $replace = array($this->uid, $this->hash, $key);
        $url = str_replace($this->search, $replace, $this->url);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);
        return json_decode($response);
    }
    function set($key, $value){
        if(!is_string($value)){
           throw(new Exception('value must be a string, not: '.print_r($value, true)));
        }
        $replace = array($this->uid, $this->hash, $key);
        $url = str_replace($this->search, $replace, $this->url);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, 'value='.$value);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);
        return json_decode($response);
    }
}
?>