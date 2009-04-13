<?php 
require_once("../../yosdk/Yahoo.inc");
require('config.inc');
$session = YahooSession::requireSession(KEY,SECRET);
$session->accessToken->consumer = KEY;
$access_token_filepath = './tokens/'.$session->guid.'_access_token.txt';
file_put_contents($access_token_filepath, json_encode($session->accessToken));
