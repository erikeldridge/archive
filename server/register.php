<?php
$filters = array(
    'submit' => FILTER_SANITIZE_STRING,
    'consumerKey' => FILTER_SANITIZE_STRING,
    'consumerSecret' => FILTER_SANITIZE_STRING,
    'providerName' => FILTER_SANITIZE_STRING,
    'openidRealmUri' => FILTER_SANITIZE_STRING,
    'openidReturnToUri' => FILTER_SANITIZE_STRING,
);
$input = filter_var_array($_GET, $filters);

if(isset($input['submit'])){
    
    //format for storage
    $obj = new stdclass();
    $obj->providerName = $input['providerName'];
    $obj->consumerKey = $input['consumerKey'];
    $obj->consumerSecret = $input['consumerSecret'];
    $obj->openidRealmUri = $input['openidRealmUri'];
    $obj->openidReturnToUri = $input['openidReturnToUri'];
    
    //init storage
    require '../../netdb/sdk.php';
    require 'secure.inc';
    $storage = new Netdb($netdbUid, $netdbSecret);
    $storageKey = $input['providerName'].'-'.$input['consumerKey'];
    $storageValue = json_encode($obj);
    
    //store obj
    $response = $storage->set($storageKey, $storageValue);
    
    //confirm success
    if('success' == $response->status){
        $value = json_decode($response->value);
    }
}
?>

<? if($value->providerName): ?>
<b>Success!</b><br/>
Here's what was saved:
<ul>
    <li><?= $value->providerName ?></li>
    <li><?= $value->consumerKey ?></li>
    <li><?= $value->consumerSecret ?></li>
    <li><?= $value->openidRealmUri ?></li>
    <li><?= $value->openidReturnToUri ?></li>
</ul>
Re-submit form to update/correct information
<p/>
<? else: ?>
<b>Register your OAuth key/secret here</b>
<? endif ?>
<form>
    Provider name, eg yahoo:<br/>
    <input name="providerName"/><br/>
    Consumer key (from provider):<br/>
    <input name="consumerKey"/><br/>
    Consumer secret (from provider):<br/>
    <input name="consumerSecret"/><br/>
    OpenID realm URI, eg http://test.erikeldidge.com:<br/>
    <input name="openidRealmUri"/><br/>
    OpenID return-to path, eg /foxbat/return_to.html:<br/>
    <input name="openidReturnToUri"/><br/>
    <input type="submit" name="submit" value="Register"/>
</form>