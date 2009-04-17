<?php
require('config.inc');
require('../yosdk/lib/Yahoo.inc');
$session_store = new CookieSessionStore();
$session = YahooSession::initSession(KEY, SECRET, APPID, TRUE, CALLBACK, $session_store);
$access_token_readable = print_r($session->accessToken, true);
//send an update
$user = $session->getSessionedUser();
$suid = 'update'.time();//just a unique string
$title = 'this is an update';
$link = 'http://github.com/erikeldridge/example/';
$user->insertUpdate($suid, $title, $link);
//create a link to the logged-in user's profile page
$profile_url = 'http://profiles.yahoo.com/u/'.$user->guid;
?>
<head>
	<link rel="stylesheet" type="text/css" href="style.css">
</head>
<h1>Success!</h1>
You have now authorized updates originating from this site to be published on Yahoo!
<p/>
An event notification has been sent to Yahoo! to demonstrate this behavior.  You can see it on <a href="<?= $profile_url ?>" target="_blank">your Yahoo! profile page</a>.
<p/>
In case you're interested, this is the access token this site can store, refresh, and use to sign requests to the Updates API:
<pre>
<?= $access_token_readable ?>	
</pre>
This token is stored in a cookie by default.  You can override this behavior by creating a PHP class that implements the YahooSessionStore interface.
<p/>
You can close this window at any time, or the window could be designed to auto-close
