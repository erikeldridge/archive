<?php
require('config.inc');
require(YOSDK_PATH);
$token = YahooAuthentication::createRequestToken(KEY, SECRET, APP_ID, BASE_URL.'/callback.php');
CookieSessionStore::storeRequestToken($token);
$url = YahooAuthentication::createAuthorizationUrl($token, BASE_URL.'/callback.php');
?>
<head>
	<link rel="stylesheet" type="text/css" href="style.css">
</head>
<h1>Let Your Friends Know What You're Up To</h1>
To do this, you're going to need to log into Yahoo! and grant this site permission to publish information about your activities on this site.  
<p/>
Ready to get started? <a href="<?= $url ?>">Log in to Yahoo!</a>
<p/>
Need more information?  
<p/>
OAuth is a simple, secure, and quick way to publish and access protected data (photos, videos, contact list). It's an open authentication model based primarily on existing standards that ensures secure credentials can be provisioned and verified by different software platforms ... <i><a href="http://developer.yahoo.com/oauth/" target="_blank">read more</a></i>