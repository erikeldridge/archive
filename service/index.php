<?php
//safely fetch input
$filters = array(
    'method' => FILTER_SANITIZE_STRING,
    'hash' => FILTER_SANITIZE_STRING
);
$input = filter_var_array($_GET, $filters);

//if no hash, return error
//todo: format all output in a way that's readable by the client, ie $data = json_encode(array('key'=>'val'));
if(!isset($input['hash'])){
    die('hash is always required');
}

//todo: validate crumb
//todo: validate crumb only in 1st request

switch($input['method']){
    case 'profile':
    
        //fetch credentials from store
        $credentials = include('credentials.php');
        
        //validate hash
        if(!isset($credentials[$input['hash']])){
           //die('invalid hash');
        }
        
        list($key, $secret) = $credentials[$input['hash']];
        
        //call api using key/secret for profile data
        $data = json_encode(array(
            'name' => 'foo bar',
            'address' => '123 first st.',
            'city' => 'burlingame',
            'state' => 'ca',
            'country' => 'usa'
        ));
        break;
        
    case 'foo':
        
        break;
        
    default:
    
        //on initial call, provide crumb only
        $crumb = md5($input['hash'].time());
        $data = json_encode(array('crumb'=>$crumb));
        break;
}
$data = urlencode($data);
$chunks = str_split($data, 10);
?>

<iframe src="../iframe.html?totalQtyChunks=<?= count($chunks) ?>"></iframe>

<? foreach($chunks as $chunk): ?>
    <iframe src="../iframe.html?<?= $chunk ?>"></iframe>
<? endforeach ?>