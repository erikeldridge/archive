<?php

/**
 * @package    http://github.com/erikeldridge/foxbat/tree/master
 * @copyright  (c) 2009, Erik Eldridge, all rights reserved
 * @license    BSD Open Source License
 *
 *   Permission is hereby granted, free of charge, to any person obtaining a copy
 *   of this software and associated documentation files (the "Software"), to deal
 *   in the Software without restriction, including without limitation the rights
 *   to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 *   copies of the Software, and to permit persons to whom the Software is
 *   furnished to do so, subject to the following conditions:
 *
 *   The above copyright notice and this permission notice shall be included in
 *   all copies or substantial portions of the Software.
 *
 *   THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 *   IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 *   FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 *   AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 *   LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 *   OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 *   THE SOFTWARE.
 **/
 
// xdr url should be set using stored association w/ app-id/oauth-key passed in
$xdrUrl = 'http://localhost/~eldridge/github/erikeldridge/iframeio/client/iframe.html';

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
    <iframe src="<?= $xdrUrl ?>?id=<?= $input['id'] ?>&index=<?= $index ?>&total=<?= $total ?>&chunk=<?= $chunk ?>"></iframe>
<? endforeach ?>