<?php
/*
Introduction:
This script is the first of a four-part series that demonstrates how to perform the 3-legged oauth authentication routine (parts 1 and 2), with a couple examples of how to use the resulting access token (parts 3 and 4), for a desktop app.  This script will perform the first leg of the routine by fetching the request token.  The script also produces a url that the user should go to in a browser to complete the second leg of the routine.  The end result is a request token stored locally in a text file.  

If you are not interested in the internal details of the routine, and are willing to use the Yahoo! PHP SDK (http://developer.yahoo.com/social/sdk/), the SDK provides a much simpler means to accomplish OAuth authentication.
 
Prerequisites:
- PHP5
- Familiarity with the OAuth authentication flow
- An OAuth "desktop" application registered in the Yahoo! Developer Dashboard (http://developer.yahoo.com/dashboard/)
- The OAuth lib from the Yahoo! PHP SDK

Overview:
- Include the PHP OAuth library shipped with the PHP SDK.  The 'requier' function is used because it will fail fatally if the library cannot be found, and the library is required for the script to operate.
- Define the consumer key and secret assigned to the app
- Sign the request url using the methods provided by the OAuth lib
- Make the request using the PHP cURL utility <link to curl on php.net>
- Save the request token somewhere persistent for use later by the access token fetching script (part 2 of the series)
- Print the url where the user needs to go to sign into Yahoo! and authorize access by the app to the user's social data

Usage:
- Save this code on your local machine
- Edit the include path for the OAuth lib to match the location on your server
- Edit the key and secret variables so they hold the key and secret associated with the app
- Run the script as follows
    - on the command line: "$php ./fetchRequestToken.php
    - in a browser, just navigate to "http://{path to script directory}/fetchRequestToken.php"
- Note the creation of a text file, requestToken.txt, in the same directory as the script
- Copy the url printed by the script
- Navigate to the address in a browser to complete the second leg of the OAuth routine
*/
require('yosdk/lib/OAuth.php');

//Step 1: key/secret from Y!
$key = '{key}';
$secret = '{secret}';

//Step 2: get req token
//prep request
$consumer = new OAuthConsumer($key, $secret);
$url = 'https://api.login.yahoo.com/oauth/v2/get_request_token';
$request = OAuthRequest::from_consumer_and_token($consumer, NULL, 'POST', $url, array());
$request->sign_request(new OAuthSignatureMethod_PLAINTEXT(), $consumer, NULL);
var_dump($request);
//make request
$ch = curl_init($url);
$options = array(
    CURLOPT_POST=> true,
	CURLOPT_POSTFIELDS => $request->to_postdata(),
	CURLOPT_RETURNTRANSFER => true
);
curl_setopt_array($ch, $options);
parse_str(curl_exec($ch), $resp);
curl_close($ch);
//extract token from response
$requestToken = new stdclass();
$requestToken->key = $resp["oauth_token"];
$requestToken->secret = $resp["oauth_token_secret"];
//save the token data somewhere persistent
file_put_contents('requestToken.txt', json_encode($requestToken));

//Step 3: direct user to Y! for auth
$url = sprintf("https://%s/oauth/v2/request_auth?oauth_token=%s", 
 'api.login.yahoo.com', 
 urlencode($requestToken->key)
);
echo sprintf("Go here and login: \n%s\n", $url);
