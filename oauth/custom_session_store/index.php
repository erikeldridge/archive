<?php 
require_once("../../yosdk/Yahoo.inc");
require_once("CustomSessionStore.inc");
require_once("config.inc");
$redirect = TRUE;  
$guid = 'BG5BMUK24OOYGHWKTJBCX2TN5E';//'{guid}';//set by app logic
$sessionStore = new CustomSessionStore($guid);
$session = YahooSession::initSession(KEY, SECRET, APPID, $redirect, CALLBACK, $sessionStore);
?>
<pre>
<? print_r($session->accessToken) ?>
</pre>
