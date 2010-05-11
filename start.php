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

$query = sprintf( "use '%s' as start; select * from start where oauthConsumerKey='%s' and openid='%s' and returnTo='%s'",
    'http://example.com/openid-oauth-yql-yui-party/start.xml',
    $_GET['oauthConsumerKey'],
	$_GET['openid'],
	'http://example.com/openid-oauth-yql-yui-party/return_to.html'
);
$response = yql( $query );

//4) redirect user to log in
if ( $response && $response->query->results->uri ) {
    header( "Location: ".$response->query->results->uri );
} else {
    header('Cache-Control: no-cache, must-revalidate');
    var_dump($response);
}
?>