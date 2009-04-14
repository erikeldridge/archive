<?php 
require_once("yosdk/Yahoo.inc");
require_once("CustomSessionStore.inc");
require_once("config.inc");
$applicationId = NULL;
$redirect = TRUE; 
$callback = ''; 
$guid = '{guid}';//set by app logic
$sessionStore = new CustomSessionStore($guid);
$session = YahooSession::initSession(KEY, SECRET, $applicationId, $redirect, $callback, $sessionStore);
?>
<pre>
<? print_r($session->accessToken) ?>
</pre>
