<?php 
require('config.inc');
require_once("yosdk/Yahoo.inc");
require_once("CustomSessionStore.inc");
$applicationId = '';
$redirect = TRUE; 
$callback = ''; 
$guid = '{guid}';//app logic would provide this
$sessionStore = new CustomSessionStore($guid);
$session = YahooSession::initSession(KEY, SECRET, $applicationId, $redirect, $callback, $sessionStore);
$user = $session->getSessionedUser(); 
$start = 0;
$count = 10;
$total = 10;
var_dump($user->getConnections($start, $count, $total));
