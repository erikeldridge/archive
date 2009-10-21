<?php
require 'request.php';

//safely fetch input
$filters = array(
    //auth identifier
    'service' => FILTER_SANITIZE_STRING,
    //request params
    'method' => FILTER_SANITIZE_STRING,
    'url' => FILTER_SANITIZE_STRING,
    'params' => FILTER_SANITIZE_STRING,
    //acct identifier
    'hash' => FILTER_SANITIZE_STRING,
    //internal params
    'id' => FILTER_SANITIZE_STRING
);
$input = filter_var_array($_GET, $filters);

//validate input
//hash is always required
if(!isset($input['hash'])){
    $data = array('error'=>"hash req'd for all requests");
}elseif(!isset($input['id'])){
    $data = array('error'=>"req index req'd for all requests");    
}
// elseif(!isset($input['crumb'])){
//     $crumb = md5($input['hash'].time());
//     $data = array('crumb'=>$crumb);
// }
elseif(isset($input['service'])){
    //service endpoints
    switch($input['service']){
        //some service requiring authentication, eg yql's social.profile table
        case 'privateco':
            $data = array('success'=>print_r($input, true));
            // die();
            //fetch credentials from store
            $store = include('store.php');
            $user = $store[$input['hash']];
            if(!isset($store[$input['hash']])){
               $data = array('error'=>'there is no record in the store for hash '.$input['hash']);
               break;
            }
            if(!$user[$input['service']]){
                $data = array('error'=>'there is no record in the store for service '.$input['service']);
                break;
            }
            list($key, $secret, $token) = $user[$input['service']];
            //prep oauth
            $url = urldecode($input['url']);
            $params = urldecode($input['params']);
            $response = request($input['method'], $url, $params);
            $data = array('success'=>$response);
            break;
        case 'public':
            break;
        default:
            //error: invalid service id
            break;
    }
}else{
    $data = array('error'=>$_GET['id']);
}

//format data for output
$data = urlencode(json_encode($data));
$chunks = str_split($data, 10);

//output markup
?>

<iframe src="../iframe.html?id=<?= $input['id'] ?>&total=<?= count($chunks) ?>"></iframe>

<? foreach($chunks as $chunk): ?>
    <iframe src="../iframe.html?id=<?= $input['id'] ?>&chunk=<?= $chunk ?>"></iframe>
<? endforeach ?>