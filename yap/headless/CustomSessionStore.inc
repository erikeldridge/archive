<?php
require_once("yosdk/YahooSessionStore.inc");

class CustomSessionStore implements YahooSessionStore{
	var $request_token_filepath;
	var $access_token_filepath;
	
	function CustomSessionStore($guid){
		$this->request_token_filepath = './tokens/'.$guid.'_request_token.txt';
		$this->access_token_filepath = './tokens/'.$guid.'_access_token.txt';
	}
	/**
	* Indicates if the session store has a request token.
	*
	* @return True if a request token is present, false otherwise.
	*/
	function hasRequestToken(){
		if(is_file($this->request_token_filepath)){
			return true;
		}else{
			return false;
		}
	}

	/**
	* Indicates if the session store has an access token.
	*
	* @return True if an access token is present, false otherwise.
	*/
	function hasAccessToken(){
		if(is_file($this->access_token_filepath)){
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
		if(file_put_contents($this->request_token_filepath, json_encode($token))){
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
		$json = file_get_contents($this->request_token_filepath);
		return json_decode($json);
		
	}

	/**
	* Clears the request token from the session store.
	*
	* @return True on success, false otherwise.
*/
	function clearRequestToken(){
		unlink($this->request_token_filepath);
	}

	/**
	* Stores the given access token in the session store.
	*
	* @param $token A PHP stdclass object containing the components of 
	*               the OAuth access token.
	* @return True on success, false otherwise.
*/
	function storeAccessToken($token){
		if(file_put_contents($this->access_token_filepath, json_encode($token))){
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
		$json = file_get_contents($this->access_token_filepath);
		return json_decode($json);
	}

	/**
	* Clears the access token from the session store.
	*
	* @return True on success, false otherwise.
*/
	function clearAccessToken(){
		unlink($this->access_token_filepath);
	}
}