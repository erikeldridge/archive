<?php 
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
if ($title && $description && $link){
	$suid = $yahoo_user->guid . time();
	$yahoo_user->insertUpdate($suid, $title, $link, $description);
	echo "Update Successful";
}
?>
