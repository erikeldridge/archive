<?php 
/*
Copyright (c) 2009 Yahoo! Inc. All rights reserved.
The copyrights embodied in the content of this file are licensed under the BSD (revised) open source license
*/
require_once("config.php");
require_once("php_sdk/Yahoo.inc");
require_once("CustomSessionStore.inc");

//capture POST data for update
$title = $_POST['title'];
$description = $_POST['description'];
$link = $_POST['link'];
$token = $_POST['token'];

//initialize session
$sessionStore = new CustomSessionStore();
$session = YahooSession::initSession(KEY, SECRET, APPID, TRUE, CALLBACK, $sessionStore);
$yahoo_user = $session->getSessionedUser();

//create new update
if ($title){
	$suid = $yahoo_user->guid . time();
	if ($yahoo_user->insertUpdate($suid, $title, $link, $description)){
		echo "Update Successful";
	}
}
?>
