<?php

/*
Copyright (c) 2009, Erik Eldridge. All rights reserved.
Code licensed under the BSD License:
http://github.com/erikeldridge/authproxy/blob/master/license.txt
*/

class KeyDB {
    
    //key => secret
    public static $credentials = array('' => '');
    public static $netdb_key = '';
    public static $netdb_secret = '';
}