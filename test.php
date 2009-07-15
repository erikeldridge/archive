<?php
if(isset($_GET['run'])){
	require('Curl.inc');
	$c = new Curl;
	
	//test default behavior
	$param = 'qwe';
	$test = $c->get('http://localhost/~pandayak/test/test.php', array('param' => $param));
	assert($param == $test);
	
	$param = 'asd';
	$test = $c->post('http://localhost/~pandayak/test/test.php', array('param' => $param));
	assert($param == $test);
	
	$param = 'zxc';
	$test = $c->put('http://localhost/~pandayak/test/test.php', array('param' => $param));
	assert($param == $test);
	
	$param = '123';
	$test = $c->delete('http://localhost/~pandayak/test/test.php', array('param' => $param));
	assert($param == $test);
	
	//test multi behavior w/ single requests
	$param = 'qwe';
	$test = $c->multi(array(
		array('get', 'http://localhost/~pandayak/test/test.php', array('param' => $param))
	));
	assert($param== $test[0]);
	
	$param = 'asd';
	$test = $c->multi(array(
		array('post', 'http://localhost/~pandayak/test/test.php', array('param' => $param))
	));
	assert($param == $test[0]);
	
	$param = 'zxc';
	$test = $c->multi(array(
		array('put', 'http://localhost/~pandayak/test/test.php', array('param' => $param))
	));
	assert($param == $test[0]);
	
	$param = '123';
	$test = $c->multi(array(
		array('delete', 'http://localhost/~pandayak/test/test.php', array('param' => $param))
	));
	assert($param == $test[0]);
	
	//test multi requests w/ multiple requests
	$params = array('qwe', 'asd', 'zxc', '123');
	$test = $c->multi(array(
		array('get', 'http://localhost/~pandayak/test/test.php', array('param' => $params[0])),
		array('post', 'http://localhost/~pandayak/test/test.php', array('param' => $params[1])),
		array('put', 'http://localhost/~pandayak/test/test.php', array('param' => $params[2])),
		array('delete', 'http://localhost/~pandayak/test/test.php', array('param' => $params[3]))
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
	//$curl = new Curl;

	print_r($curl->get('google.com')->headers);
}