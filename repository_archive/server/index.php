<?php
/**
 * Source: http://github.com/erikeldridge/iframeio/tree/master
 * Copyright: (c) 2009, Erik Eldridge, all rights reserved
 * License: BSD Open Source License http://gist.github.com/375593
 **/
function respond( $data, $id, $xdrUrl ){
    
    //format data for output
    $data = urlencode(json_encode($data));

    //split output into chunks of arbitrary size (pending length avail. for GET params)
    $size = 2000;
    $chunks = str_split($data, $size);
    $total = count($chunks);
    
    header('Content-Type: text/html');
    
    foreach($chunks as $index => $chunk){
        printf( '<iframe src="%s?id=%s&index=%s&total=%d&chunk=%s"></iframe>', $xdrUrl, $id, $index, $total, $chunk );
    }
    
    exit;
}

//fetch input
$filters = array(
    'oauthConsumerKey' => FILTER_SANITIZE_STRING,
    'method' => FILTER_SANITIZE_STRING,
    'url' => FILTER_SANITIZE_STRING,
    'params' => FILTER_SANITIZE_STRING,
    
    //internal params
    'id' => FILTER_SANITIZE_STRING
);
$input = filter_var_array($_GET, $filters);

// if we don't have a frame id, the whole response mechanism is broken.  
// this should only happen for completely invalid requests
if(!isset($input['id'])){
    header('Content-Type: text/plain');
    die( "iframe id req'd for all requests" );
}

// xdr url should be set using stored association w/ app-id/oauth-key passed in
$apps = array(
    'asd123' => array(
        'xdrUrl' => 'http://localhost/~eldridge/github/erikeldridge/iframeio/client/iframe.html'
    )
);

// if we don't have an app defined for ck, return error
if (!isset($apps[ $input['oauthConsumerKey'] ]['xdrUrl'])) {
    header('Content-Type: text/plain');
    die( 'app undefined for oauthConsumerKey: '.$input['oauthConsumerKey'] );
}

//sample request handler
if('get' == $input['method']){
    $url = urldecode($input['url']);
    $params = urldecode($input['params']);
    $ch = curl_init($url.'?'.$params);    
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    $response = curl_exec($ch);
    curl_close($ch);
    
    //format response for json encode
    $data = array('response'=>$response);
    
    respond( $data, $input['id'], $apps[ $input['oauthConsumerKey'] ]['xdrUrl'] ); 
}

?>