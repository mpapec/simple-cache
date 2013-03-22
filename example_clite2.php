<?php
/*
>>> Usage with blocks
(You can use Cache_Lite for caching blocks and not the whole page)
*/

require_once('Cache/Lite.php');

$options = array(
    'cacheDir' => '/tmp/',
    'lifeTime' => 3600
);

// Create a Cache_Lite object
$Cache_Lite = new Cache_Lite($options);

if ($data = $Cache_Lite->get('block1')) {
    echo($data);
} else { 
    $data = 'Data of the block 1';
    $Cache_Lite->save($data);
}

echo('<br><br>Non cached line !<br><br>');

if ($data = $Cache_Lite->get('block2')) {
    echo($data);
} else { 
    $data = 'Data of the block 2';
    $Cache_Lite->save($data);
}

