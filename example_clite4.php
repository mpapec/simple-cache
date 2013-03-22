<?php
/*
>>> Usage with blocks :
(You can use Cache_Lite_Output for caching blocks and not the whole page)
*/

require_once('Cache/Lite/Output.php');

$options = array(
    'cacheDir' => '/tmp/',
    'lifeTime' => 10
);

$cache = new Cache_Lite_Output($options);

if (!($cache->start('block1'))) {
    // Cache missed...
    echo('Data of the block 1 !<br>');
    $cache->end();
}

echo('<br><br>Non cached line !<br><br>');

if (!($cache->start('block2'))) {
    // Cache missed...
    echo('Data of the block 2 !<br>');
    $cache->end();
}
