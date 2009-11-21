# OAuthPanda

## Preamble

OAuthPanda attempts to make requests to [OAuth](http://oauth.net)-secured web services as painless as possible.  OAuthPanda would like to be simple as a spoon to use, but tough when it comes to protecting the user.

OAuthPanda uses the standard OAuth library and an http request library to do the heavy lifting.  The panda's magic comes from it's ability to make these two talk to each other and communicate with people as clearly as possible.  Detailed installation and usage instructions are given below.

## Prerequisites

* PHP 5.2 with [cURL](http://us.php.net/manual/en/ref.curl.php) enabled
* The [Yahoo! Curl utility class](http://github.com/yahoo/yos-social-php5/blob/master/lib/Yahoo/YahooCurl.class.php)
* A server that can serve content to an OAuth provider, i.e., that's accessible via a domain you have root access to.  
   * Confused?  Here's some documentation for getting started w/ [Yahoo!](http://developer.yahoo.com/oauth/).
* OAuth consumer key and secret.  
   * The linked documentation above explains this too
* The [OAuth PHP library]()

## Usage

### Fetching the OAuth request token

    <?php
    require_once 'OAuth.php';
    require_once 'YahooCurl.class.php';
    require_once 'OAuthPanda.class.php';
    
    $key = '{your OAuth consumer key}';
    $secret = '{your OAuth consumer secret}';
    $callback_url = '{your OAuth callback url}';

    $panda = new OAuthPanda($key, $secret);
    $response = $panda->set('oauth_param_location', 'url')->GET(
        'https://api.login.yahoo.com/oauth/v2/get_request_token', 
        array('oauth_callback' => $callback_url)
    );
    printf('<br/>===<pre>%s</pre>===<br/>', print_r($response, true));
    ?>
    
## License

OAuthPanda

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