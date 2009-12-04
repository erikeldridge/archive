# OauthPanda

## Preamble

OauthPanda attempts to make requests to [OAuth](http://oauth.net)-secured web services as painless as possible.  The panda acts as a soft layer between an HTTP request client, a standard OAuth client, and a user.  

OauthPanda relies on HttpRequestWrapper and OauthWrapper, to manage the HTTP request and OAuth clients, respectively.  All OauthPanda knows is that the user feeds it a set of required and optional parameters, OauthLibPanda uses some of them to create an OAuth signature, and HttpRequestPanda uses others to make the actual request.  The panda's magic comes from it's ability to make these two talk to each other and communicate with people as clearly as possible.  

OauthPanda is simple as a spoon to use, but detailed installation and usage instructions are given below.

## Prerequisites

* PHP 5.2 with [cURL](http://us.php.net/manual/en/ref.curl.php) enabled
* The [Yahoo! Curl utility class](http://github.com/yahoo/yos-social-php5/blob/master/lib/Yahoo/YahooCurl.class.php).
* A server that can serve content to an OAuth provider, i.e., that's accessible via a domain you have root access to.  
   * Confused?  Here's some documentation for getting started w/ [Yahoo!](http://developer.yahoo.com/oauth/).
* OAuth consumer key and secret.  
   * The linked documentation above explains this too
* The [OAuth PHP library](http://oauth.googlecode.com/svn/code/php/OAuth.php)

## Usage

### Fetching the OAuth request token

    <?php
	 //see example.php for full code
	 //...
    $response = $foo->GET(array(
        'url' => 'https://api.login.yahoo.com/oauth/v2/get_request_token',
        'params' => array('oauth_callback' => OAUTH_CALLBACK_URL)
    ));

    //extract token
    parse_str($response['response_body'], $request_token_response);

    //sanity check
    assert(isset($request_token_response['oauth_token']));

    //standard oauth lib expects request token stdclass obj
    $request_token = (object) array(
        'key' => $request_token_response['oauth_token'],
        'secret' => $request_token_response['oauth_token_secret']
    );

    //cache token for retreival after auth
    file_put_contents('request_token.txt', serialize($request_token));

    //redirect user for auth
    $redirect_url = sprintf(
        'https://api.login.yahoo.com/oauth/v2/request_auth?oauth_token=%s&oauth_callback=%s',
    	$request_token_response['oauth_token'], 
    	urlencode(OAUTH_CALLBACK_URL)
    );
    header('Location: '.$redirect_url);
	 //...
    ?>
    
## License

OauthPanda

* package: http://github.com/erikeldridge/oauthpanda
* author: Erik Eldridge
* copyright: Copyrights for code authored by Erik Eldridge is licensed under the following terms:
* license: BSD Open Source License

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.