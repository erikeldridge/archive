Purpose:
This app demonstrates how to easily integrate Yahoo! Updates into an arbitrary website's functionality using the Yahoo! PHP SDK.  

Requirements:
- PHP 5
- A "Web-based" OAuth application registered with the Yahoo! Developer Network (http://developer.yahoo.com)
- The Yahoo! PHP SDK (http://developer.yahoo.com/social/sdk)

Usage:
- Upload to a directory to your server.  For this example, I'll call this directory "sdk_updates".
- Edit the file called "config.inc" to define constant variables called "KEY", "SECRET", "APP_ID", and "BASE_URL" for your app's key, secret, application id, and url, respectively.  These values can be found in your app's description in the YDN developer dashboard (http://developer.yahoo.com/dashboard).  Note: the callback url must be rooted in the domain you verified for your app.  Also, edit the variable definition for "YOSDK_PATH" to point to the location of the "Yahoo.inc" file in the Yahoo! PHP SDK on your server.
- Navigate to the url of sdk_updates/index.php to start the demonstration.
- The demonstration supposes that index.php is a settings page for your site.  Click on the checkbox next to "Keep My Friends Updated On Yahoo!".  This will launch a popup window to explain what the user needs to do.
- Click the link to "Log in to Yahoo!".  This will forward you to the Yahoo! authentication page to start the OAuth authorization flow.
- Grant access to the app so it can publish updates for your account.  You will then be forwarded to the callback.php file located at the base url you defined in the config.inc file.  Callback.php will publish a sample update to your Yahoo! account.
- Click the link to "your Yahoo! profile page" view the update on your Yahoo! profile page.  THis same update is also pushed to all Yahoo! products that accept updates.  Currently, this list includes profiles.yahoo.com, mail.yahoo.com, and the Yahoo! Updates API webservice.
- Close the popup window.  That's it!

Notes:
- You can demo this code here: http://example.erikeldridge.com/oauth/sdk_updates/

License:
Software License Agreement (BSD License)
Copyright (c) 2009, Yahoo! Inc.
All rights reserved.

Redistribution and use of this software in source and binary forms, with or without modification, are permitted provided that the following conditions are met:

    * Redistributions of source code must retain the above copyright notice, this list of conditions and the
      following disclaimer.
    * Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following disclaimer in the documentation and/or other materials provided with the distribution.
    * Neither the name of Yahoo! Inc. nor the names of its contributors may be used to endorse or promote products derived from this software without specific prior written permission of Yahoo! Inc.

THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.