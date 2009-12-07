<?php

require 'tests.php';
$tests = get_class_methods('Tests');

?>

<head>
    <link rel="stylesheet" type="text/css" href="http://yui.yahooapis.com/combo?3.0.0/build/cssreset/reset-min.css&3.0.0/build/cssfonts/fonts-min.css&3.0.0/build/cssgrids/grids-min.css">
    
    <style>
    body {
        text-align: left;
    }
    ul {
       margin: 1ex;
    }
    li {
        width: 10em;
        padding: 1ex;
    }
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
            Y.Node.get('#' + data.id).addClass(data.result);
            Y.Node.get('#' + data.id).set('innerHTML', html);
        };

    	Y.on('io:complete', complete, this);
	
    	for (var i = 0; i < tests.length; i++) {
            Y.io('api.php?test=' + tests[i]);	
    	}
    });

    </script>
</body>