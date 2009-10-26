<?php
$filters = array(
    'submit' => FILTER_SANITIZE_STRING,
    'consumerKey' => FILTER_SANITIZE_STRING,
    'consumerSecret' => FILTER_SANITIZE_STRING,
    'providerName' => FILTER_SANITIZE_STRING
);
$input = filter_var_array($_GET, $filters);

if(isset($input['submit'])){
    
    //format for storage
    $obj = new stdclass();
    $obj->providerName = $input['providerName'];
    $obj->consumerKey = $input['consumerKey'];
    $obj->consumerSecret = $input['consumerSecret'];
    
    //init storage
    require '../../netdb/sdk.php';
    require 'secure.inc';
    $storage = new Netdb($netdbUid, $netdbSecret);
    $storageKey = $input['providerName'].$input['consumerKey'];
    $storageValue = json_encode($obj);
    
    //store obj
    $response = $storage->set($storageKey, $storageValue);
    
    //confirm success
    if('success' == $response->status){
        $displaySuccess = true;
    }else{
        $displaySuccess = false;   
    }
}
?>

<? if($displaySuccess): ?>
<b>Success!</b><br/>
Here's what was saved:
<ul>
    <li><?= $input['providerName'] ?></li>
    <li><?= $input['consumerKey'] ?></li>
    <li><?= $input['consumerSecret'] ?></li>
</ul>
Re-submit form to update/correct information
<p/>
<? else: ?>
<b>Register your OAuth key/secret here</b>
<? endif ?>
<form>
    Provider name:<br/>
    <input name="providerName"/><br/>
    Consumer key (from provider):<br/>
    <input name="consumerKey"/><br/>
    Consumer secret (from provider):<br/>
    <input name="consumerSecret"/><br/>
    <input type="submit" name="submit" value="Register"/>
</form>