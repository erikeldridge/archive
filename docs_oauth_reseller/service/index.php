<?php
error_reporting(E_ALL);
require_once("config.php");
require_once("{path to PHP SDK}/lib/Yahoo.inc");
require_once("CustomSessionStore.inc");

$store = new CustomSessionStore(array(
	'third_party_callback' => isset($_GET['third_party_callback']) ? urldecode($_GET['third_party_callback']) : NULL,
	'oauth_token' => isset($_GET['oauth_token']) ? urldecode($_GET['oauth_token']) : NULL
));
$verifier = isset($_GET['oauth_verifier']) ? $_GET['oauth_verifier'] : NULL;
$session = YahooSession::requireSession(KEY, SECRET, APPID, CALLBACK, $store, $verifier);

if($session && $session->guid){	
	header(sprintf('Location: %s?status=success&guid=%s', $session->accessToken->third_party_callback, $session->accessToken->guid));
}else{
	header(sprintf('Location: %s?status=error', $session->accessToken->third_party_callback));
}
?>