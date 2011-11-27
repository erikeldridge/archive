<?php
/*
Copyright (c) 2009 Yahoo! Inc. All rights reserved.
The copyrights embodied in the content of this file are licensed under the BSD (revised) open source license
*/
class CustomSessionStore{
	function CustomSessionStore($params){
		foreach($params as $key => $val){
			$this->$key = $val;
		}
		//init db
		if (!$this->db = new SQLiteDatabase('sqlite')) {
			//error
		}
		//verify table exists
		$sql = 'SELECT * FROM tokens WHERE key = \'foo\';';
		$result = $this->db->query($sql);
		if(false === $result){
			$sql = 'CREATE TABLE tokens (key TEXT, token TEXT); 
				INSERT INTO tokens (key, token) VALUES (\'foo\', \'bar\');';
		    $this->db->queryExec($sql);
		}
	}
	/**
	* Indicates if the session store has a request token.
	*
	* @return True if a request token is present, false otherwise.
	*/
	function hasRequestToken(){
		if(!isset($this->oauth_token)){
			return false;
		}
		$sql = sprintf("SELECT * FROM tokens WHERE key = '%s';", $this->oauth_token);
		$result = $this->db->query($sql)->fetch();
		if(false === $result){
			return false;
		}else{
			return true;
		}
	}

	/**
	* Indicates if the session store has an access token.
	*
	* @return True if an access token is present, false otherwise.
	*/
	function hasAccessToken(){
		if(!isset($this->guid)){
			return false;
		}
		$sql = sprintf('SELECT token FROM tokens WHERE key = \'%s\';', $this->guid);
		$result = $this->db->query($sql)->fetch();
		if($result[0]){
			return true;
		}else{
			return false;
		}
	}

	/**
	* Stores the given request token in the session store.
	*
	* @param $token A PHP stdclass object containing the components of 
	*               the OAuth request token.
	* @return True on success, false otherwise.
*/
	function storeRequestToken($token){
		$token->third_party_callback = $this->third_party_callback;//store url to redirect back to after the oauth callback
		$sql = sprintf('INSERT INTO tokens (key, token) VALUES (\'%s\', \'%s\');', $token->key, serialize($token));
		if($this->db->queryExec($sql)){
			return true;
		}else{
			return false;
		}
	}

	/**
	* Fetches and returns the request token from the session store.
	*
	* @return The request token.
*/
	function fetchRequestToken(){
		$sql = sprintf('SELECT token FROM tokens WHERE key = \'%s\';', $this->oauth_token);
		$result = $this->db->query($sql)->fetch();
		if($result[0]){
			$token = unserialize($result[0]);
			$this->third_party_callback = $token->third_party_callback;
			return $token;
		}else{
			return false;
		}
	}

	/**
	* Clears the request token from the session store.
	*
	* @return True on success, false otherwise.
*/
	function clearRequestToken(){
		$sql = sprintf('DELETE FROM tokens WHERE key = \'%s\';', $this->oauth_token);
		if($this->db->queryExec($sql)){
			return true;
		}else{
			return false;
		}
	}

	/**
	* Stores the given access token in the session store.
	*
	* @param $token A PHP stdclass object containing the components of 
	*               the OAuth access token.
	* @return True on success, false otherwise.
*/
	function storeAccessToken($token){
		$this->guid = $token->guid;
		$token->third_party_callback = $this->third_party_callback;
		$sql = sprintf('INSERT INTO tokens (key, token) VALUES (\'%s\', \'%s\');', 
			$token->guid, serialize($token));
		if($this->db->queryExec($sql)){
			return true;
		}else{
			return false;
		}
	}

	/**
	* Fetches and returns the access token from the session store.
	*
	* @return The access token.
*/
	function fetchAccessToken(){
		$sql = sprintf('SELECT token FROM tokens WHERE key = \'%s\';', $this->guid);
		$result = $this->db->query($sql)->fetch();
		return unserialize($result[0]);
	}

	/**
	* Clears the access token from the session store.
	*
	* @return True on success, false otherwise.
*/
	function clearAccessToken(){
		$sql = sprintf('DELETE FROM token WHERE key = \'%s\';', $this->guid);
		if($this->db->queryExec($sql)){
			return true;
		}else{
			return false;
		}
	}
}
?>