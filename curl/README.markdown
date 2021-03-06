Curl, CurlResponse
==================

Sean Huber [http://github.com/shuber](http://github.com/shuber)

Description
-----------

A basic CURL wrapper for PHP (see [http://php.net/curl](http://php.net/curl) for more information about the libcurl extension for PHP)


Installation
------------

	git clone git://github.com/shuber/curl.git


Usage
-----

### Initialization

Simply require and initialize the Curl class like so

	require_once 'curl.php';
	$curl = new Curl;

### Performing a Request

The Curl object supports 4 types of requests: GET, POST, PUT, and DELETE. You must specify a url to request and optionally specify an associative array of variables to send along with it.

	$response = $curl->get($url, $vars = array()); # The Curl object will append the array of $vars to the $url as a query string
	$response = $curl->post($url, $vars = array());
	$response = $curl->put($url, $vars = array());
	$response = $curl->delete($url, $vars = array());
	$response = $curl->multi(array(
		array('get', $url, $vars = array()),
		array('post', $url, $vars = array()),
		array('put', $url, $vars = array()),
		array('delete', $url, $vars = array())
	));
	
Examples

	$response = $curl->get('google.com?q=test');

	# The Curl object will append '&some_variable=some_value' to the url
	$response = $curl->get('google.com?q=test', array('some_variable' => 'some_value'));
	
	$response = $curl->post('test.com/posts', array('title' => 'Test', 'body' => 'This is a test'));

	$response = $curl->multi(array(
		array('get', 'http://www.google.com/search?', $vars = array('q' => 'thisthat')),
		array('get', 'http://search.yahoo.com/search?', array('p' => 'thisthat')),
	));
All requests return a CurlResponse object (see below)

### The CurlResponse Object

A normal CURL request will return the headers and the body in one response string. This class parses the two and places them into separate properties.

For example

	$response = $curl->get('google.com');
	echo $response->body; # A string containing everything in the response except for the headers
	print_r($response->headers); # An associative array containing the response headers

Which would display something like

	<html>
	<head>
	<title>Google.com</title>
	</head>
	<body>
	Some more html...
	</body>
	</html>

	Array
	(
	    [Http-Version] => 1.0
	    [Status-Code] => 200
	    [Status] => 200 OK
	    [Cache-Control] => private
	    [Content-Type] => text/html; charset=ISO-8859-1
	    [Date] => Wed, 07 May 2008 21:43:48 GMT
	    [Server] => gws
	    [Connection] => close
	)
	
The CurlResponse class defines the magic [__toString()](http://php.net/__toString) method which will return the response body, so `echo $response` is the same as `echo $response->body`

### Cookie Sessions

By default, cookies will be stored in a file called `curl_cookie.txt`. You can change this file's name by setting it like this

	$curl->cookie_file = 'some_other_filename';

This allows you to maintain a session across requests

### Basic Configuration Options

You can easily set the referer or user-agent

	$curl->referer = 'http://google.com';
	$curl->user_agent = 'some user agent string';
	
You may even set these headers manually if you wish (see below)

### Setting Custom Headers

You can set custom headers to send with the request

	$curl->headers['Host'] = 12.345.678.90;
	$curl->headers['Some-Custom-Header'] = 'Some Custom Value';

For multi-curl requests, you can set the headers for each request
	$curl->multi(array(
		array('get', 'http://example.com', $vars = array(), $headers = array('Accept' => 'application/json'))
	));
	
### Setting Custom CURL request options

You can set/override many different options for CURL requests (see the [curl_setopt documentation](http://php.net/curl_setopt) for a list of them)

	# any of these will work
	$curl->options['AUTOREFERER'] = true;
	$curl->options['autoreferer'] = true;
	$curl->options['CURLOPT_AUTOREFERER'] = true;
	$curl->options['curlopt_autoreferer'] = true;


Contact
-------

Problems, comments, and suggestions all welcome: [shuber@huberry.com](mailto:shuber@huberry.com)