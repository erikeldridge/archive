<?php
require_once("config.php");
require_once("php_sdk/Yahoo.inc");

$token = YahooAuthorization::getRequestToken(KEY, SECRET, CALLBACK);
//stash token in db
if ($db = new SQLiteDatabase('sqlite')) {
	$sql = 'SELECT * FROM tokens WHERE key = \'foo\';';
	$result = @$db->query($sql);
	if(false === $result){
		$sql = 'CREATE TABLE tokens (key TEXT, token TEXT); 
			INSERT INTO tokens (key, token) VALUES (\'foo\', \'bar\');';
	    $db->queryExec($sql);
	}
	$token->third_party_callback = urldecode($_GET['third_party_callback']);//store url to redirect back to after the oauth callback
	$sql = sprintf('INSERT INTO tokens (key, token) VALUES (\'%s\', \'%s\');', $token->key, serialize($token));
    $db->queryExec($sql);
} else {
    //die($err);
}
$auth_url = YahooAuthorization::createAuthorizationUrl($token);
header('Location: '.$auth_url);
?>