<?php
/*
Introduction:
This script is the second of a four-part series that demonstrates how to perform the 3-legged oauth authentication routine (parts 1 and 2), with a couple examples of how to use the resulting access token (parts 3 and 4), for a desktop app.  This script will perform the third leg of the routine by fetching the access token.  It assumes there is a stored request token from the first script in the series.  The end result is a access token stored locally in a text file.  

If you are not interested in the internal details of the OAuth "dance", and are willing to use the Yahoo! PHP SDK (http://developer.yahoo.com/social/sdk/), the SDK provides a much simpler means to accomplish OAuth authentication.
 
Prerequisites:
- PHP5
- Familiarity with the OAuth authentication flow
- An OAuth "desktop" application registered in the Yahoo! Developer Dashboard (http://developer.yahoo.com/dashboard/)
- The OAuth lib from the Yahoo! PHP SDK
- A stored, valid request token in a text file called "requestToken.txt".  If the request token doesn't work, try running fetchRequestToken.php (part 1) again to generate and store a fresh token.

Overview:
- Include the PHP OAuth library shipped with the PHP SDK.  The 'require' function is used because it will fail fatally if the library cannot be found, and the library is required for the script to operate.
- Define the consumer key and secret assigned to the app
- Sign the request url using the methods provided by the OAuth lib
- Make the request using the PHP cURL utility (http://us.php.net/curl)
- Set the token values for a couple expiration times
    - Set the token expiration to -1, if undefined
    - Set the request handle expiration to -1, if undefined.  This is a field unique to Yahoo! that governs how long we can use the access token before it must be refreshed.
- Save the access token somewhere persistent for use later by the getStatus.php or setStatus.php scripts (parts 3 and 4 of the series)

Usage:
- Save this code on your local machine
- Edit the include path for the OAuth lib to match the location on your server
- Edit the key and secret variables so they hold the key and secret associated with the app
- Run the script as follows
    - on the command line: "$php ./fetchAccessToken.php
    - in a browser, just navigate to "http://{path to script directory}/fetchAccessToken.php"
- Note the creation of a text file, accessToken.txt, in the same directory as the script
- After the access token is stored, you are free to start making OAuth-signed requests
*/
require('yosdk/lib/OAuth.php');

//key/secret from step 1
$key = '{key}';
$secret = '{secret}';

//Step 4: get access token
//extract request token from storage
$requestToken = json_decode(file_get_contents('requestToken.txt'));//error-tip: if token invalid, re-fetch request token
//prep request
$consumer = new OAuthConsumer($key, $secret);
$url = 'https://api.login.yahoo.com/oauth/v2/get_token';
//prep request
$request = OAuthRequest::from_consumer_and_token($consumer, $requestToken, 'POST', $url, array());
$request->sign_request(new OAuthSignatureMethod_HMAC_SHA1(), $consumer, $requestToken);
$headers = array(
	"Accept: application/json"
);
//make request
$ch = curl_init($url);
$options = array(
    CURLOPT_POST=> true,
	CURLOPT_POSTFIELDS => $request->to_postdata(),
	CURLOPT_RETURNTRANSFER => true
);
curl_setopt_array($ch, $options);
parse_str(curl_exec($ch), $response);
curl_close($ch);
//extract token params from response
$now = time();
$accessToken = new stdclass();
$accessToken->key = $response["oauth_token"];
$accessToken->secret = $response["oauth_token_secret"];
$accessToken->guid = $response["xoauth_yahoo_guid"];
$accessToken->consumer = $consumerKey;
$accessToken->sessionHandle = $response["oauth_session_handle"];

// Check to see if the access token ever expires.
if(array_key_exists("oauth_expires_in", $response)) {
    $accessToken->tokenExpires = $now + $response["oauth_expires_in"];
}
else {
    $accessToken->tokenExpires = -1;
}

// Check to see if the access session handle ever expires.
if(array_key_exists("oauth_authorization_expires_in", $response)) {
    $accessToken->handleExpires = $now + $response["oauth_authorization_expires_in"];
}
else {
    $accessToken->handleExpires = -1;
}
//save the token data somewhere persistent
file_put_contents('accessToken.txt', json_encode($accessToken));
//now use token to make API requests