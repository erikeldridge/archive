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

class TestUtils
{
    static function respond($test_name, $result, $message=null)
    {
        assert(is_string($test_name));
        assert(is_string($result));
        assert(is_string($message) || is_null($message));
        
        $response = (object) array(
            'result' => 'pass',
            'test_name' => $test_name
        );
        
        if (false === is_null($message)) {
            $response->message = $message;
        }
        
        echo json_encode($response);
    }
    
    static function assertTrue($value, $message=null)
    {
        assert(is_bool($value));
        assert(is_string($message) || is_null($message));
        
        //determine test via stack to avoid unintuitively requiring test name be passed as arg to this fn
        $backtrace = debug_backtrace();
        $first_trace_item = array_pop($backtrace);
        
        if (true === $value) {
            self::respond($first_trace_item['function'], 'pass', $message);
        } else {
            self::respond($first_trace_item['function'], 'fail', $message);
        }
    }
}

// note: 'result' field must have corresponding css class for styles to be applied
class Tests
{
    function test1()
    {
        TestUtils::assertTrue(1 === 1);
    }
    
    function test2()
    {
        TestUtils::assertTrue(1 === 2);
    }
}
?>