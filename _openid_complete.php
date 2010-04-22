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
    'assert' => FILTER_SANITIZESTRING
);
$input = filter_var_array( $_REQUEST, $filters );

$assoc_store = new SqliteStore('assoc');
$assoc_db_key = md5( $input['key'].$input['id'] );
$assoc_json = $assoc_store->get( $assoc_db_key );

if ( !$assoc_json ) {
   die('association is required');
}

$parsed_assert = parse_url( $input['assert'] ); 
$local_url = $parsed_assert['scheme'].'://'.$parsed_assert['host'].$parsed_assert['path'];
parse_str( $parsed_assert['query'], $parsed_query );
$assert_json = json_encode( $parsed_query );

//verify assertion
$verify_query = sprintf( 
    "use 'http://github.com/yql/yql-tables/raw/master/openid/openid.verify.xml' as openid.verify;"
    ."select * from openid.verify where localUrl = '%s' and assertJson = '%s' and assocJson = '%s'", 
    $local_url, $assert_json, $assoc_json 
);

//kludge: php does weird things to inputs w/ periods & json w/ slashes
$find = array( 'openid_', 'oauth_', 'ns_', 'pape_auth_level_nist' );
$replace = array( 'openid.', 'oauth.', 'ns.', 'pape.auth_level.nist' );
$verify_query = str_replace( $find, $replace, $verify_query );
$verify_query = stripslashes( $verify_query );

$verify_results = yql( $verify_query );

if ( !$verify_results->query->results->success ) {
    printf( '<pre>%s</pre>', print_r( $input['assert'], true ) );
    die('signature invalid');
}
// echo 'relay.html?request_token='.$parsed_query['openid_oauth_request_token'];
header('Location: relay.html?request_token='.$parsed_query['openid_oauth_request_token']);
?>