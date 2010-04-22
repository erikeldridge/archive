<?php

require 'SqliteStore.php';

function yql( $query )
{
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

$filters = array(
    'key' => FILTER_SANITIZESTRING,
    'id' => FILTER_SANITIZESTRING,
    'return_to' => FILTER_SANITIZESTRING
);
$input = filter_var_array( $_REQUEST, $filters );

$assoc_store = new SqliteStore('assoc');
$assoc_db_key = md5( $input['key'].$input['id'] );
$assoc_json = $assoc_store->get( $assoc_db_key );

//get assoc if it's not in the db, or if it's expired
$time = time();
if ( !$assoc_json || json_decode( $assoc_json )->expires < $time ) {
    $assoc_query = sprintf( 
        "use 'http://gist.github.com/yql/yql-tables/raw/master/openid/openid.assoc.xml' as openid.assoc;"
        ."use 'http://gist.github.com/yql/yql-tables/raw/master/openid/openid.discover.xml' as openid.discover;"
        ."use 'http://gist.github.com/yql/yql-tables/raw/master/openid/openid.normalize.xml' as openid.normalize;"
        ."select * from openid.assoc where uri in ( select success from openid.discover where normalizedId in ( select id from openid.normalize where id = '%s' ) )", $input['id']
    );
    $assoc_results = yql( $assoc_query );
    $assoc = $assoc_results->query->results->success;
    $assoc->expires = $time + $assoc->expires_in;
    $assoc_store->set( $assoc_db_key, json_encode( $assoc ) );
} else {
   $assoc = json_decode( $assoc_json );
}

//fetch openid log in url
$login_query = sprintf( 
    "use 'http://gist.github.com/yql/yql-tables/raw/master/openid/openid.xml' as openid;"
    ."select * from openid where id = '%s' and return_to = '%s' and assoc_handle = '%s' and oauthKey = '%s'", 
    $input['id'], urldecode( $input['return_to'] ), $assoc->assoc_handle, $input['key']
);
$login_results = yql( $login_query );

//redirect to log in
header( 'Location: '.$login_results->query->results->success );
?>