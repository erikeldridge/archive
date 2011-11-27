usage:
- upload the "custom_session_store" directory and its contents to a server
- in the YDN dashboard, create an "OAuth App" and set the base url to point to:
"{url to your server}/custom_session_store".
- create a file called "config.inc" and define() your OAuth app key, secret, and id and your app's callback url in it
- upload the YOSDK (http://developer.yahoo.com/social/sdk/) to your server 
- edit the include paths in "custome_session_store/index.php" and "CustomSessionStore.inc" to reflect the location of the YOSDK and the config.inc files
- in index.php, define the "guid" variable match the logged-in-user guid associated with the access token.
- create a directory called "tokens" that's writable by your app in the "custom_session_store" directory
- in your browser, navigate to "{url to your server}/custom_session_store"  This will create a file called:
"tokens/{logged-in-user guid}_request_token.txt" and redirect you to log into Yahoo!.  After logging in and authorizing the app, you will be redirected back to index.php as per the callback url you defined in config.inc.  Index.php will then write a file called
"tokens/{logged-in-user guid}_request_token.txt" that contains the access token parameters the custom_session_store app will use to access private user data.
- on the command line, navigate to the "custom_session_store" directory and run the "part_2.php" file, e.g., $ php part_2.php.  This will read the access token stored in the "tokens" directory, make a request for the connections of the user represented by the guid, and dump the data to the command line.

notes:
- By default, the access token data is written to a cookie.  We are overriding this behavior by implementing the YahooSessionStore interface in the CustomSessionStore class.  This code stores the access token for each user in a text file so we can use it without access to a browser.  The access token could also be stored in a database.
- The access token will be refreshed on demand by the SDK.  This is discussed in step 5 of the Yahoo! OAuth documentation here:
http://developer.yahoo.com/oauth/guide/oauth-auth-flow.html

license:
Software License Agreement (BSD License)
Copyright (c) 2009, Yahoo! Inc.
All rights reserved.

Redistribution and use of this software in source and binary forms, with or without modification, are permitted provided that the following conditions are met:

    * Redistributions of source code must retain the above copyright notice, this list of conditions and the
      following disclaimer.
    * Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following disclaimer in the documentation and/or other materials provided with the distribution.
    * Neither the name of Yahoo! Inc. nor the names of its contributors may be used to endorse or promote products derived from this software without specific prior written permission of Yahoo! Inc.

THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.

