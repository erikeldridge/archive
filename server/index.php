<?php

//fetch input
$filters = array(
    
    //request params
    'method' => FILTER_SANITIZE_STRING,
    'url' => FILTER_SANITIZE_STRING,
    'params' => FILTER_SANITIZE_STRING,
    
    //internal params
    'id' => FILTER_SANITIZE_STRING
);
$input = filter_var_array($_GET, $filters);

//validate input
if(!isset($input['id'])){
    $data = array('error'=>"iframe id req'd for all requests");    
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
}

//format data for output
$data = urlencode(json_encode($data));

//split output into chunks of arbitrary size (pending length avail. for GET params)
$size = 100;
$chunks = str_split($data, $size);
$total = count($chunks);

//output markup
?>

<? foreach($chunks as $index => $chunk): ?>
    <iframe src="http://localhost/~eldridge/foxbat/client/iframe.html?id=<?= $input['id'] ?>&index=<?= $index ?>&total=<?= $total ?>&chunk=<?= $chunk ?>"></iframe>
<? endforeach ?>