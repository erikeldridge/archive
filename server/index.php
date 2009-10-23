<?php

$filters = array(
    'action' => FILTER_SANITIZE_STRING,
    'hash' => FILTER_SANITIZE_STRING,
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

switch($input['action']){
    //some service requiring authentication, eg yql's social.profile table
    case 'setOauthAccessToken':
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
    case 'fetchHybridAuthUrl':
    
        //settings
        $openidIncludePath = './php-openid-2.1.3/';
        
        //fetch key
        $store = include('store.php');
        $service = $store[$input['hash']];
        
        //BEGIN: generate openid+oauth redirect url
        
        //format incl path as assumed by openid lib 
        $includePath = get_include_path().PATH_SEPARATOR
            .$openidIncludePath;
        set_include_path($includePath);
        
        require_once 'Auth/OpenID/Consumer.php';
        require_once 'Auth/OpenID/FileStore.php';
        require_once 'Auth/OpenID/SReg.php';
        require_once 'Auth/OpenID/PAPE.php';
        $openidFileStore = new Auth_OpenID_FileStore('/tmp/');
        $openidConsumer =& new Auth_OpenID_Consumer($openidFileStore);

        //this could just as easily be set dynamically
        $openidIdentifier = 'yahoo.com';
        $openidAuthRequest = $openidConsumer->begin($openidIdentifier);

        //Add simple reg support.
        //Note: domains implementing Yahoo! hybrid auth must be whitelisted
        $openidSimpleRegRequest = Auth_OpenID_SRegRequest::build(
            
            // req'd
            array('nickname'), 
            
            // optional
            array('fullname', 'email')
        );
        $openidAuthRequest->addExtension($openidSimpleRegRequest);

        //url for openid provider log in page
        $openidLoginRedirectUrl = $openidAuthRequest->redirectURL(
            $service['openidRealmUri'],
            $service['openidReturnToUri']
        );

        //add hybrid auth fields
        $additionalFields = array(
            'openid.ns.oauth' => 'http://specs.openid.net/extensions/oauth/1.0',
            'openid.oauth.consumer' => $service['oauthKey']
        );

        $openidLoginRedirectUrl .= '&'.http_build_query($additionalFields);
        //END: generate openid+oauth redirect url
        
        $data = array('status'=>'success','url'=>$openidLoginRedirectUrl);
        break;
    default:
        //error: invalid service id
        break;
}

//format data for output
$data = urlencode(json_encode($data));
$size = 10;
$chunks = str_split($data, $size);

//output markup
?>

<iframe src="http://localhost/~eldridge/foxbat/client/iframe.html?id=<?= $input['id'] ?>&total=<?= count($chunks) ?>"></iframe>

<? foreach($chunks as $chunk): ?>
    <iframe src="http://localhost/~eldridge/foxbat/client/iframe.html?id=<?= $input['id'] ?>&chunk=<?= $chunk ?>"></iframe>
<? endforeach ?>