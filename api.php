<?php

require 'tests.php';

header('Content-Type: application/javascript');

$tests = new Tests;
$tests->{$_GET['test']}();

?>