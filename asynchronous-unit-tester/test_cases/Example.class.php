<?php

require_once 'TestUtils.class.php';

class Example
{
    function test1()
    {        
        $ch = curl_init("http://example.com?foo=bar");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);
        TestUtils::assertTrue(
            isset($response)
        );
    }
}

?>