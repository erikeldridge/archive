<?php
/*
This file will make a series of curl requests to itself using the Curl class when run as described below.

How to run:
- place this file in a location where it can be rendered by php and served to a browser, eg on your server
- redefine $url to point to the location of this file, eg http://localhost/test.php
- run this script by browsing to it and appending a 'run' parameter to the url, eg http://localhost/test.php?run
*/
$url = 'http://localhost/~pandayak/curl/test.php';
if(isset($_GET['run'])){
	require('curl.php');
	$c = new Curl;
	//test default behavior
	$param = 'qwe';
	$test = $c->get($url, array('param' => $param));
	assert($param == $test);
	
	$param = 'asd';
	$test = $c->post($url, array('param' => $param));
	assert($param == $test);
	
	$param = 'zxc';
	$test = $c->put($url, array('param' => $param));
	assert($param == $test);
	
	$param = '123';
	$test = $c->delete($url, array('param' => $param));
	assert($param == $test);
	
	//test multi behavior w/ single requests
	$param = 'qwe';
	$test = $c->multi(array(
		array('get', $url, array('param' => $param))
	));
	assert($param== $test[0]);
	
	$param = 'asd';
	$test = $c->multi(array(
		array('post', $url, array('param' => $param))
	));
	assert($param == $test[0]);
	
	$param = 'zxc';
	$test = $c->multi(array(
		array('put', $url, array('param' => $param))
	));
	assert($param == $test[0]);
	
	$param = '123';
	$test = $c->multi(array(
		array('delete', $url, array('param' => $param))
	));
	assert($param == $test[0]);
	
	//test multi requests w/ multiple requests
	$params = array('qwe', 'asd', 'zxc', '123');
	$test = $c->multi(array(
		array('get', $url, array('param' => $params[0])),
		array('post', $url, array('param' => $params[1])),
		array('put', $url, array('param' => $params[2])),
		array('delete', $url, array('param' => $params[3]))
	));
	
	assert($params[0] == $test[0]);
	assert($params[1] == $test[1]);
	assert($params[2] == $test[2]);
	assert($params[3] == $test[3]);
	
	echo '<pre>';
	print_r($test);
	echo '</pre>';
	
	//original test content
	//require 'curl.php';
	$curl = new Curl;

	print_r($curl->get('google.com')->headers);
}

switch($_SERVER['REQUEST_METHOD']){
	case 'GET':
		echo $_GET['param'];
		break;
	case 'POST':
		echo $_POST['param'];
		break;
	case 'PUT':
		$str = file_get_contents("php://input");
		parse_str($str, $input);
		echo $input['param'];
		break;
	case 'DELETE':
		$str = file_get_contents("php://input");
		parse_str($str, $input);
		echo $input['param'];
		break;
}