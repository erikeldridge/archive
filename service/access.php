<?php
require_once("config.php");
require_once("php_sdk/Yahoo.inc");

if(isset($_GET['oauth_verifier'])){
	if ($db = new SQLiteDatabase('sqlite')) {
		//extract req params
		$token = $_GET['oauth_token'];
		$verifier = $_GET['oauth_verifier'];
		//fetch stored req token
		$sql = sprintf('SELECT token FROM tokens WHERE key = \'%s\';', $token);
		$result = $db->query($sql)->fetch();
		$token = unserialize($result[0]);
		$third_party_callback = $token->third_party_callback;
		//request and store access token
		$token = YahooAuthorization::getAccessToken(KEY, SECRET, $token, $verifier);
		$sql = sprintf('INSERT INTO tokens (key, token) VALUES (\'%s\', \'%s\');', 
			$token->guid, serialize($token));
	    $db->queryExec($sql);
		header(sprintf('Location: %s?guid=%s', $third_party_callback, $token->guid));
	} else {
	    //die($err);
	}
}
?>