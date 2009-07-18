<?php
/*
Copyright (c) 2009 Yahoo! Inc. All rights reserved.
The copyrights embodied in the content of this file are licensed under the BSD (revised) open source license
*/
class CustomSessionStore{
	function CustomSessionStore(){}
	/**
	* Indicates if the session store has a request token.
	*
	* @return True if a request token is present, false otherwise.
	*/
	function hasRequestToken(){
		if($_COOKIE["yosRequestToken"]){
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
		if($_COOKIE["yosAccessToken"] || $_POST['token']){
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
		if (setcookie("yosRequestToken", serialize($token), time()+3600)){
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
		$contents = $_COOKIE["yosRequestToken"];
		return unserialize(stripslashes($contents));
	}

	/**
	* Clears the request token from the session store.
	*
	* @return True on success, false otherwise.
*/
	function clearRequestToken(){
		setcookie("yosRequestToken", "", time()-3600);
	}

	/**
	* Stores the given access token in the session store.
	*
	* @param $token A PHP stdclass object containing the components of 
	*               the OAuth access token.
	* @return True on success, false otherwise.
*/
	function storeAccessToken($token){
		if(setcookie("yosAccessToken", serialize($token), time()+3600)){
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
		$contents = ($_COOKIE["yosAccessToken"]) ? $_COOKIE["yosAccessToken"] : $_POST['token'];
		return unserialize(stripslashes($contents));
	}

	/**
	* Clears the access token from the session store.
	*
	* @return True on success, false otherwise.
*/
	function clearAccessToken(){
		setcookie("yosAccessToken", "", time()-3600);
	}
}
?>