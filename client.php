<?php

/*
Unpredictable Unit Tester

* package: http://github.com/erikeldridge/unpredictable-unit-tester
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
*/

require 'tests.php';
$tests = get_class_methods('Tests');

?>

<head>
    <link rel="stylesheet" type="text/css" href="http://yui.yahooapis.com/combo?3.0.0/build/cssreset/reset-min.css&3.0.0/build/cssfonts/fonts-min.css&3.0.0/build/cssgrids/grids-min.css">
    
    <style>
    body {
        text-align: left;        
        padding: 1ex;
    }
    h1 {
        font-weight: bold;
        margin-bottom: 1ex;
    }
    li {
        width: 10em;
        padding: 1ex;
        margin-bottom: 1ex;
    }
    
    /* note: these must correspond w/ 'result' field returned from server*/
    .pass {
        color: white;
        background-color: green;
    }
    .fail {
        background-color: red;
    }
    </style>
</head>
<body>
    <h1>results:</h1>
    <ul id="results">
        <? foreach($tests as $test): ?>
            <li id="<?= $test ?>"><?= $test ?>: loading ...</li>
        <? endforeach ?>
    </ul>

    <script src="http://yui.yahooapis.com/3.0.0/build/yui/yui-min.js"></script>
    <script>
    YUI().use('node', 'io-base', 'json-parse', function(Y) {
    
        var tests = <?= json_encode($tests) ?>;
    
        function complete(id, o, args) {
            var data = Y.JSON.parse(o.responseText),
                html = data.id + ': ' + data.result;
                
            if (data.message) {
                html += ' (' + data.message + ')';
            }
                
            Y.Node.get('#' + data.id).addClass(data.result).set('innerHTML', html);
        };

    	Y.on('io:complete', complete, this);
	
    	for (var i = 0; i < tests.length; i++) {
            Y.io('server.php?test=' + tests[i]);	
    	}
    });

    </script>
</body>