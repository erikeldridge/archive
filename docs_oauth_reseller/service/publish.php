<?php 
/*
Copyright (c) 2009 Yahoo! Inc. All rights reserved.
The copyrights embodied in the content of this file are licensed under the BSD (revised) open source license
*/
require_once("config.php");
require_once("php_sdk/Yahoo.inc");
require_once("CustomSessionStore.inc");

//capture POST data for update
$guid = $_POST['guid'];
$title = $_POST['title'];
$description = $_POST['description'];
$link = $_POST['link'];

//initialize session
$sessionStore = new CustomSessionStore($guid);
$session = YahooSession::initSession(KEY, SECRET, APPID, TRUE, CALLBACK, $sessionStore, null);
$yahoo_user = $session->getSessionedUser();

//create new update
if ($title){
	$suid = $yahoo_user->guid . time();
	if ($yahoo_user->insertUpdate($suid, $title, $link, $description)){
		echo "Update Successful";
	}
}
?>
