<?php
//safely fetch input
$filters = array(
    'method' => FILTER_SANITIZE_STRING,
    'hash' => FILTER_SANITIZE_STRING,
    'time' => FILTER_SANITIZE_STRING,
    'crumb' => FILTER_SANITIZE_STRING
);
$input = filter_var_array($_GET, $filters);

switch($input['method']){
    case 'profile':
        //todo: validate crumb
        
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
        
        //require hash on initial req
        if(!isset($input['hash'])){
            $data = json_encode(array('error'=>"hash req'd for init request"));
            break;
        }
    
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