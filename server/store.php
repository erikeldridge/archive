<?php
return array(
    //hash1 => service => {name, url, auth type, key (opt), secret (opt)}, hash2 => ...
    'asd456' => array(
        'key'=>'qwe', 
        'secret'=>'123', 
        'token'=>'',
        'openidRealmUri' => 'http://test.erikeldridge.com',
        'openidReturnToUri' => 'http://test.erikeldridge.com/foxbatexample/client/return_to.html'
    ),
    'qwe123' => array(
        'key'=>'foo', 
        'secret'=>'baz', 
        'token'=>'',
        'openidRealmUri' => 'http://sample.com',
        'openidReturnToUri' => 'http://sample.com/foxbatexample/client/return_to.html'
    ),
);
?>