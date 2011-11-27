<?php 
require_once("../yosdk/Yahoo.inc");
require('config.inc');
$session = YahooSession::requireSession(KEY,SECRET);
$session->accessToken->consumer = KEY;
$access_token_filepath = './tokens/'.$session->guid.'_access_token.txt';
file_put_contents($access_token_filepath, json_encode($session->accessToken));
$access_token_readable = print_r($session->accessToken, true);
?>

Here is the access token data:
<pre>
<?= $access_token_readable ?>
</pre>
It's stored as JSON here:<br/>
<?= $access_token_filepath ?>

