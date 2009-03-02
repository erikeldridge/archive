<?php
/*
Motivation:
- To demonstrate signature generation with as simple code as possible in the hope that it illustrates the process in a manner somewhat comparable to signature generators in other languages

Preconditions:
- PHP 5
- Familiarity with YAP
- A pre-existing YAP app
- Server space to host this file

Usage:
- Copy this code and paste it into a file on your server
- Edit the code to use your app's consumer secret
- In the developer dashboard, preview your app
- In the preview, you should see the two keys formated like:
YQ8d7DAg8d0mSLt1yCMVPCpVF1o=

YQ8d7DAg8d0mSLt1yCMVPCpVF1o=

1

- The trailing '1' indicates that the keys match

Notes:
- This file references OAuth.php and Yahoo.inc from the Yahoo! PHP SDK (http://developer.yahoo.com/social/sdk/), but does not rely on those files.
- This does not validate the timestamp.  Be aware that the timestamp field passed from YAP is called yap_time, not oauth_timestamp
*/

$secret='e75af55c075d11b61d2a4c1b124b6af80803f9cb';//'{YAP app secret from developer.yahoo.com/dashboard}';

//ref: OAuthUtil::urlencodeRFC3986, OAuth.php
function urlencodeRFC3986($string) {
  return str_replace('%7E', '~', rawurlencode($string));
}
//ref: OAuthRequest::get_normalized_http_url, OAuth.php
function get_normalized_http_url($url) {
    $parts = parse_url($url);
    $port = (isset($parts['port']) && $parts['port'] != '80') ? ':' . $parts['port'] : '';
    $path = (isset($parts['path'])) ? $parts['path'] : '';
    return $parts['scheme'] . '://' . $parts['host'] . $port . $path;
}

$scheme = (!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] != "on") ? 'http' : 'https';
$url = $scheme . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];//note: this is the direct url to this file, not the YAP url to the file

$remote_signature = $_POST['oauth_signature'];//signature from incoming request
echo sprintf('<p>%s</p>', print_r($remote_signature, true));//display the incoming signature for debugging
unset($_POST['oauth_signature']);//delete field as per OAuthRequest::get_signable_parameters(), OAuth.php

//make sure everything passed in POST data is encoded and formatted as it would be when generating a request
$keys = array_map('urlencodeRFC3986', array_keys($_POST));
$values = array_map('urlencodeRFC3986', array_values($_POST));
$params = array_combine($keys, $values);
ksort($params);
foreach($params as $key => $value){
    $pairs[] = "$key=$value";
}
// construct base string
$base_string_parts = array(
    'POST',
    get_normalized_http_url($url),
    implode('&', $pairs)
);
$base_string_parts = array_map('urlencodeRFC3986', $base_string_parts);
$base_string = implode($base_string_parts, '&');
//url encode and stringify consumer & access keys
$key_parts = array(
    $secret,
    ''// this is two-legged oauth, so we don't need a token as per YahooSession::checkSignature(), Yahoo.inc
);
$key_parts = array_map('urlencodeRFC3986', $key_parts);
$key = implode('&', $key_parts);

$local_signature = base64_encode(hash_hmac('sha1', $base_string, $key, true));//ref: OAuthSignatureMethod_HMAC_SHA1::build_signature(), OAuth.php
echo sprintf('<p>%s</p>', print_r($local_signature, true));//display the generated signature for debugging

echo sprintf('<p>%s</p>', print_r($local_signature == $remote_signature, true));//display the comparison for debugging