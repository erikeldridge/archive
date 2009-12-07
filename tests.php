<?php
class Tests
{
    function test1()
    {
        $result = (object) array(
            'id' => __FUNCTION__,
            'result' => 'pass'
        );
        
        echo json_encode($result);
    }
}
?>