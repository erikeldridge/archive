<?php

/**
 * @package    http://github.com/erikeldridge/foxbatexample/tree/master
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
 
return array(
    //hash1 => service => {name, url, auth type, key (opt), secret (opt)}, hash2 => ...
    'asd456' => array(
        'key'=>'dj0yJmk9aWhaR09McnVxUVBQJmQ9WVdrOU56RlaMGxXTjJzbWNHbzlNVEl5TWprE16a3gmcz1jb25zdW1lnNlY3JldCZ4PTI', 
        'secret'=>'3a68d4bfbd7c0221ce0ce9520e1b0f0235d86', 
        'token'=>'',
        'openidRealmUri' => 'http://test.erikeldridge.com',
        'openidReturnToUri' => 'http://test.erikeldridge.com/foxbatexample/client'
    ),
    'qwe123' => array(
        'key'=>'foo', 
        'secret'=>'baz', 
        'token'=>'',
        'openidRealmUri' => 'http://sample.com',
        'openidReturnToUri' => 'http://sample.com/foxbatexample/client/iframe.html'
    ),
);
?>