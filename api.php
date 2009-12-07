<?php
class Runner
{
    function test1 ()
    {
        $result = (object) array(
            'id' => $_GET['test'],
            'result' => 'pass'
        );
        $json = json_encode($result);
        echo "callback($json);";
    }
}

$runner = new Runner;
$runner->{$_GET['test']}();
?>