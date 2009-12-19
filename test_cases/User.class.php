<?php

require_once 'TestUtils.class.php';

class User
{
    //get req w/o action
    static function test1()
    {        
        require '../../curl/curl.php';
        $curl = new Curl;
        try {
            $key = 'test';
            $time = time();
            $hash = md5('secret'.$key.$time);
            $response = $curl->get(
                "http://test.erikeldridge.com/static_frontend/api/?user_id=3206cc4f7ccd4e6aa8319417f4706e80&key=$key&time=$time&hash=$hash"
            );
            $data = json_decode($response->body);
            TestUtils::assertTrue(
                isset($data->error) && false !== strpos($data->error, 'invalid action')
            );
        } catch (Exception $e) {
            
            //no exception should be thrown
            TestUtils::assertTrue(
                false,
                ''.print_r($e, true)
            );
        }
    }
    
    //get req w/ valid action
    static function test2()
    {        
        require '../../curl/curl.php';
        $curl = new Curl;
        try {
            $key = 'test';
            $time = time();
            $hash = md5('secret'.$key.$time);
            $response = $curl->get(
                "http://test.erikeldridge.com/static_frontend/api/?action=select&user_id=3206cc4f7ccd4e6aa8319417f4706e80&key=$key&time=$time&hash=$hash"
            );
            $json = json_decode($response->body);
            TestUtils::assertTrue(
                property_exists($json->data, '3206cc4f7ccd4e6aa8319417f4706e80')
                // , print_r($json, true)
            );
        } catch (Exception $e) {
            
            //no exception should be thrown
            TestUtils::assertTrue(
                false,
                ''.print_r($e, true)
            );
        }
    }
    
    //perform valid project insert
    static function test3()
    {        
        require '../../curl/curl.php';
        $curl = new Curl;
        try {
            $key = 'test';
            $time = time();
            $hash = md5('secret'.$key.$time);
            $user_id = '3206cc4f7ccd4e6aa8319417f4706e80';
            $project_id = 'c3c5b73374c970295e5243f93ae32840';
            $project_details = array(
                'name' => 'foo',
                'url' => 'http://example.com'
            );
            $response = $curl->post(
                "http://test.erikeldridge.com/static_frontend/api/?action=insert&user_id=$user_id&project_id=$project_id&key=$key&time=$time&hash=$hash",
                array('project_details' => json_encode($project_details))
            );
            $json = json_decode($response->body);
            
            //using hacky assert because asynch unit test doesn't support multiple assertions in a test
            assert('success' == $json->status);
            
            //verify project details inserted correctly
            $response = $curl->get(
                "http://test.erikeldridge.com/static_frontend/api/?action=select&user_id=3206cc4f7ccd4e6aa8319417f4706e80&key=$key&time=$time&hash=$hash"
            );
            $json = json_decode($response->body);
            TestUtils::assertTrue(
                $project_details['name'] == $json->data->$project_id->name 
                    && $project_details['url'] == $json->data->$project_id->url
            );
            
        } catch (Exception $e) {
            
            //no exception should be thrown
            TestUtils::assertTrue(
                false,
                ''.print_r($e, true)
            );
        }
    }
    
    //perform valid project delete
    static function test4()
    {        
        require '../../curl/curl.php';
        $curl = new Curl;
        try {
            $key = 'test';
            $time = time();
            $hash = md5('secret'.$key.$time);
            $user_id = '3206cc4f7ccd4e6aa8319417f4706e80';
            $url = 'http://example.com';
            $project_id = 'c3c5b73374c970295e5243f93ae32840';
            $response = $curl->post(
                "http://test.erikeldridge.com/static_frontend/api/?action=delete&user_id=$user_id&project_id=$project_id&key=$key&time=$time&hash=$hash"
            );
            $json = json_decode($response->body);
            
            //using hacky assert because asynch unit test doesn't support multiple assertions in a test
            assert('success' == $json->status);
            
            //verify project deleted
            $response = $curl->get(
                "http://test.erikeldridge.com/static_frontend/api/?action=select&user_id=3206cc4f7ccd4e6aa8319417f4706e80&key=$key&time=$time&hash=$hash"
            );
            $json = json_decode($response->body);
            TestUtils::assertTrue(
                false === isset($json->data->$project_id)
                // , print_r($json, true)
            );
            
        } catch (Exception $e) {
            
            //no exception should be thrown
            TestUtils::assertTrue(
                false,
                ''.print_r($e, true)
            );
        }
    }
}

?>