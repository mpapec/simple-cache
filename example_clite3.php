<?php
/*
A few examples of Cache_Lite_Output using :
-------------------------------------------

>>> Basic one :
*/

require_once('Cache/Lite/Output.php');

$options = array(
    'cacheDir' => '/tmp/',
    'lifeTime' => 10
);

$cache = new Cache_Lite_Output($options);

if (!($cache->start('123'))) {
    // Cache missed...
    for($i=0;$i<1000;$i++) { // Making of the page...
        echo('0123456789');
    }
    $cache->end();
}
