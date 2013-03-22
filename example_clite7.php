<?php
/*
>>> With static method :

*/
require_once('Cache/Lite/Function.php');

$options = array(
    'cacheDir' => '/tmp/',
    'lifeTime' => 10
);

$cache = new Cache_Lite_Function($options);

$cache->call('bench::static_method_to_bench', 12, 45);

class bench
{
    var $test;

    function static_method_to_bench($arg1, $arg2) {
        echo "This is the output of the function static_method_to_bench($arg1, $arg2) !<br>";
        return "This is the result of the function static_method_to_bench($arg1, $arg2) !<br>";
    }
}


