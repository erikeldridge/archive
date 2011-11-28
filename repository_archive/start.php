<?php

// ref: http://gist.github.com/raw/373487/deec0db86b6692536e9e171698e6b8fdf1c9e6ef/gistfile1.php
function yql( $query ) {
    $params = array(
        'q' => $query,
        'debug' => 'true',
        'diagnostics' => 'true',
        'format' => 'json',
        'callback' => ''
    );
    $url = 'https://query.yahooapis.com/v1/public/yql?'.http_build_query( $params );
    $ch = curl_init( $url );
    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
    $json = curl_exec( $ch );
    $response = json_decode( $json );
    curl_close( $ch );
    return $response;
}

// look up app definition to get return_to uri
$key = 'app-'.$_GET['oauthConsumerKey'];
$hash = md5( 'secret'.$key );

// jsonkv src: http://gist.github.com/376323
$uri = 'http://example.com/openid-oauth-yql-yui-party/jsonkv.php?'.http_build_query( array(
    'key'=>$key, 
    'hash'=>$hash
) );

// reuse yql fn so we don't have to write all the curl config
$query = "select * from json where url='$uri'";
$response = yql( $query );

if ( !$response->query->results->return_to ) {
    die('error: OAuth app definition required');
}

// pass off control to start.xml
$query = sprintf( "use '%s' as start; select * from start where oauthConsumerKey='%s' and openid='%s' and returnTo='%s'",
    'http://example.com/authparty/start.xml',
    $_GET['oauthConsumerKey'],
	$_GET['openid'],
	$response->query->results->return_to
);
$response = yql( $query );

// redirect user to log in
if ( $response && $response->query->results->uri ) {
    header( "Location: ".$response->query->results->uri );
} else {
    
    // dump error & be sure it's not cached
    header('Cache-Control: no-cache, must-revalidate');
    var_dump($response);
}
?>