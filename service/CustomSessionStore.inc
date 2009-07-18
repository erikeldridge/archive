<?php
/*
Copyright (c) 2009 Yahoo! Inc. All rights reserved.
The copyrights embodied in the content of this file are licensed under the BSD (revised) open source license
*/
class CustomSessionStore{
	function CustomSessionStore($guid){
		$this->guid = $guid;
		if (!$this->db = new SQLiteDatabase('sqlite')) {
			//error
		}
	}
	/**
	* Indicates if the session store has a request token.
	*
	* @return True if a request token is present, false otherwise.
	*/
	function hasRequestToken(){
	}

	/**
	* Indicates if the session store has an access token.
	*
	* @return True if an access token is present, false otherwise.
	*/
	function hasAccessToken(){
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
	}

	/**
	* Fetches and returns the request token from the session store.
	*
	* @return The request token.
*/
	function fetchRequestToken(){
	}

	/**
	* Clears the request token from the session store.
	*
	* @return True on success, false otherwise.
*/
	function clearRequestToken(){
	}

	/**
	* Stores the given access token in the session store.
	*
	* @param $token A PHP stdclass object containing the components of 
	*               the OAuth access token.
	* @return True on success, false otherwise.
*/
	function storeAccessToken($token){
		$sql = sprintf('INSERT INTO tokens (key, token) VALUES (\'%s\', \'%s\');', 
			$token->guid, serialize($token));
		return $this->db->queryExec($sql);
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
		return $this->db->queryExec($sql);
	}
}
?>