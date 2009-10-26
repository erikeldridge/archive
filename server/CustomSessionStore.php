<?php

class CustomSessionStore implements YahooSessionStore {
    function __construct($netdb, $netdbKey){
        $this->db = $netdb;
        $this->key = $netdbKey;
    }
    /**
     * Indicates if the session store has a request token.
     *
     * @return True if a request token is present, false otherwise.
     */
    function hasRequestToken(){
        return false;
    }

    /**
     * Indicates if the session store has an access token.
     *
     * @return True if an access token is present, false otherwise.
     */
    function hasAccessToken(){
        $result = $this->db->get($this->key);
        $value = json_decode($result->value);
        return ('success' == $result->status && isset($value->token));
    }

    /**
     * Stores the given request token in the session store.
     *
     * @param $token A PHP stdclass object containing the components of 
     *               the OAuth request token.
     * @return True on success, false otherwise.
     */
    function storeRequestToken($token){
        return true;
    }

    /**
     * Fetches and returns the request token from the session store.
     *
     * @return The request token.
     */
    function fetchRequestToken(){
        return true;
    }

    /**
     * Clears the request token from the session store.
     *
     * @return True on success, false otherwise.
     */
    function clearRequestToken(){
        return true;
    }

    /**
     * Stores the given access token in the session store.
     *
     * @param $token A PHP stdclass object containing the components of 
     *               the OAuth access token.
     * @return True on success, false otherwise.
     */
    function storeAccessToken($token){
        //fetch value
        $result = $this->db->get($this->key);
        $value = json_decode($result->value);
        
        //update token
        $value->token = $token;
        
        //store the result back
        $result = $this->db->set($this->key, json_encode($value));
        return ('success' == $result->status);
    }

    /**
     * Fetches and returns the access token from the session store.
     *
     * @return The access token.
     */
    function fetchAccessToken(){
        $result = $this->db->get($this->key);
        return json_decode($result->value)->token;
    }

    /**
     * Clears the access token from the session store.
     *
     * @return True on success, false otherwise.
     */
    function clearAccessToken(){
        return true;//$this->storeAccessToken('');
    }
}

?>
