<?php
/*
>>> With method :

*/
require_once('Cache/Lite/Function.php');

$options = array(
    'cacheDir' => '/tmp/',
    'lifeTime' => 10
);

$cache = new Cache_Lite_Function($options);

$obj = new bench();
$obj->test = 666;

$cache->call('obj->method_to_bench', 12, 45);

class bench
{
    var $test;

    function method_to_bench($arg1, $arg2)
    {
        echo "\$obj->test = $this->test and this is the output of the method \$obj->method_to_bench($arg1, $arg2) !<br>";
        return "\$obj->test = $this->test and this is the result of the method \$obj->method_to_bench($arg1, $arg2) !<br>";
    }

}


