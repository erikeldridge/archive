<?php

/*
Asynchronous Unit Tester

* package: http://github.com/erikeldridge/asynchronous-unit-tester
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

// fetch the names of all the files defining test cases
$file_names = glob('test_cases/*.class.php');

foreach ($file_names as $file_name) {
    require $file_name;
    
    //determine the name of the test case class from the file name
    $case_name = str_replace(array('test_cases/', '.class.php'), '', $file_name);
    
    $test_cases[] = array(
        'case_name' => $case_name,
        
        //determine the names of all the tests to run inside each class
        'test_names' => get_class_methods($case_name)
        
    );
}
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
        width: 20em;
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
    
    <!-- display the results of each test case -->
    <? foreach($test_cases as $test_case): ?>
        <h1>test case <i><?= $test_case['case_name'] ?></i> results:</h1>
        <ul id="results">
            <? foreach($test_case['test_names'] as $test_name): ?>
                <li id="<?= $test_case['case_name'].$test_name ?>"><?= $test_case['case_name'].$test_name ?>: loading ...</li>
            <? endforeach ?>
        </ul>
    <? endforeach ?>

    <script src="http://yui.yahooapis.com/3.0.0/build/yui/yui-min.js"></script>
    <script>
    YUI().use('node', 'io-base', 'json-parse', function(Y) {
    
        var test_cases = <?= json_encode($test_cases) ?>;
    
        function complete(id, o, args) {
            var data = Y.JSON.parse(o.responseText),
                html = data.test_name + ': ' + data.result;
                
            if (data.message) {
                html += ' (' + data.message + ')';
            }
            
            Y.Node.get('#' + data.case_name + data.test_name).addClass(data.result).set('innerHTML', html);
        };

    	Y.on('io:complete', complete, this);
	
	    //for each test case, fire a request for each test
    	for (var i = 0; i < test_cases.length; i++) {
    	    for (var j = 0; j < test_cases[i]['test_names'].length; j++) {
                Y.io('server.php?case_name=' + test_cases[i]['case_name'] + '&test_name=' + test_cases[i]['test_names'][j]);	
            }
    	}
    });

    </script>
</body>