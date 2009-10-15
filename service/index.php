<?php
//safely fetch input
$filters = array(
    'serviceId' => FILTER_SANITIZE_STRING,
    'hash' => FILTER_SANITIZE_STRING,
    'crumb' => FILTER_SANITIZE_STRING
);
$input = filter_var_array($_GET, $filters);

//perform negative validation
//hash is always required
if(!isset($input['hash'])){
    $data = json_encode(array('error'=>"hash req'd for all requests"));
}

//if service
if(isset($input['serviceId'])){

    if(!isset($input['crumb'])){
        $data = json_encode(array('error'=>"crumb req'd for all service requests"));
    }else{
        //validate crumb using hash and crumb store
        //if invalid crumb -> error
    }
    
    //service endpoints
    switch($input['serviceId']){

        //some service requiring authentication, eg yql's social.profile table
        case 'privateco':
            //hash => services => {serviceId1 => {name, url, auth type, key (opt), secret (opt)}, serviceId2 => ...}

            //fetch credentials from store
            $store = include('store.php');

            //validate hash
            if(!isset($store[$input['hash']])){
               //die('invalid hash');
            }elseif(!isset($store[$input['serviceId']])){

            }

            list($key, $secret) = $store[$input['hash']];

            //call api using key/secret for profile data
            $data = json_encode(array(
                'name' => 'foo bar',
                'address' => '123 first st.',
                'city' => 'burlingame',
                'state' => 'ca',
                'country' => 'usa'
            ));
            break;

        case 'public':

            break;

        default:
            //error: invalid service id
            break;
    }
    
//init
}else{
    $crumb = md5($input['hash'].time());
    $data = json_encode(array('crumb'=>$crumb));
}

//format data for output
$data = urlencode($data);
$chunks = str_split($data, 10);

//output markup
?>

<iframe src="../iframe.html?totalQtyChunks=<?= count($chunks) ?>"></iframe>

<? foreach($chunks as $chunk): ?>
    <iframe src="../iframe.html?<?= $chunk ?>"></iframe>
<? endforeach ?>