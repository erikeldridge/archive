<?php

/*
Copyright (c) 2009, Erik Eldridge. All rights reserved.
Code licensed under the BSD License:
http://github.com/erikeldridge/authproxy/blob/master/license.txt
*/

$filters = array(
    'submit' => FILTER_SANITIZE_STRING,
    'localKey' => FILTER_SANITIZE_STRING,
    'consumerKey' => FILTER_SANITIZE_STRING,
    'consumerSecret' => FILTER_SANITIZE_STRING,
    'providerName' => FILTER_SANITIZE_STRING,
    'callbackUrl' => FILTER_SANITIZE_STRING,
);
$input = filter_var_array($_GET, $filters);

if(isset($input['submit'])){
    
    require 'keydb.php';
    
    //http://github.com/shuber/curl
    require '../curl/curl.php';
    $curl = new Curl;
    
    $url = sprintf('%s/authproxy/api.php', 'http://localhost/~eldridge');
    $params = array(
        'action' => 'insert',
        'hash' => sha1(KeyDB::$credentials[$input['localKey']].$input['localKey']),
        'userId' => $input['localKey'],
        'type' => 'oauth',
        'providerName' => $input['providerName'],
        'consumerKey' => $input['consumerKey'],
        'consumerSecret' => $input['consumerSecret'],
        'callbackUrl' => $input['callbackUrl']
    );
    $response = json_decode($curl->post($url, $params)->body);
    
    //confirm success
    if('success' == $response->status){
        $params = array(
            'hash' => sha1(KeyDB::$credentials[$input['localKey']].$input['localKey']),
            'userId' => $input['localKey'],
            'type' => 'oauth',
            'recordId' => $response->recordId
        );
        $response = json_decode($curl->get($url, $params)->body);
    }
}
?>

<? if($response->value->providerName): ?>
<b>Success!</b><br/>
Here's what was saved:
<ul>
    <li><?= $response->value->providerName ?></li>
    <li><?= $response->value->consumerKey ?></li>
    <li><?= $response->value->consumerSecret ?></li>
    <li><?= $response->value->callbackUrl ?></li>
</ul>
Re-submit form to update/correct information
<p/>
<? else: ?>
<b>Register your OAuth key/secret here</b>
<? endif ?>
<form>
    Local key<br/>
    <input name="localKey"/><br/>
    Provider name, eg yahoo:<br/>
    <input name="providerName"/><br/>
    Consumer key (from provider):<br/>
    <input name="consumerKey"/><br/>
    Consumer secret (from provider):<br/>
    <input name="consumerSecret"/><br/>
    Callback URL, eg http://example.com<br/>
    <input name="callbackUrl"/><br/>
    <input type="submit" name="submit" value="Register"/>
</form>