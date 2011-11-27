<?php

class TestUtils
{
    static function respond($case_name, $test_name, $result, $message=null)
    {
        assert(is_string($test_name));
        assert(is_string($result));
        assert(is_string($message) || is_null($message));
        
        $response = (object) array(
            'result' => $result,
            'case_name' => $case_name,
            'test_name' => $test_name
        );
        
        if (true === is_string($message)) {
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
        
        //determine test case name from file name
        $case_name = str_replace(array(dirname($backtrace[0]['file']).'/', '.class.php'), '', $backtrace[0]['file']);
        
        //map result to string 
        if (true === $value) {
            $css_class_name = 'pass';
        } else {
            $css_class_name = 'fail';
        }
        
        self::respond($case_name, $first_trace_item['function'], $css_class_name, $message);
    }
}

?>