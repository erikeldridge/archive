<?php

if (!class_exists('NetDB')) {
    throw new Exception('NetDB class (http://github.com/erikeldridge/netdb/) required');
}

class NetDBStore implements KVStore {
    function __construct($key, $secret) {
        $this->netdb = new NetDB($key, $secret, 'http://test.erikeldridge.com');
    }
    function get($key) {
        $value = $this->netdb->get($key);
        return $value;
        
    }
    function set($key, $value){
        $value = $this->netdb->set($key, $value);
        return $value;
    }
}
