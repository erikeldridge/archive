<?php
//safely fetch input
$filters = array(
    'service' => FILTER_SANITIZE_STRING,
    'hash' => FILTER_SANITIZE_STRING,
    'crumb' => FILTER_SANITIZE_STRING,
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
            //hash => services => {service1 => {name, url, auth type, key (opt), secret (opt)}, service2 => ...}

            //fetch credentials from store
            $store = include('store.php');

            //validate hash
            if(!isset($store[$input['hash']])){
               //die('invalid hash');
            }elseif(!isset($store[$input['service']])){

            }

            list($key, $secret) = $store[$input['hash']];

            //call api using key/secret for profile data
            $data = array(
                'id'=>$input['id']
                // 'name' => 'foo bar',
                //                 'address' => '123 first st.',
                //                 'city' => 'burlingame',
                //                 'state' => 'ca',
                //                 'country' => 'usa'
            );
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