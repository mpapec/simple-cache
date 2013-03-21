<?php

// Include the package
require_once('Cache/Lite.php');

// Set a id for this cache
$id = '123';

// Set a few options
$options = array(
    'cacheDir' => '/tmp/',
    'lifeTime' => 30,
);

// Create a Cache_Lite object
$Cache_Lite = new Cache_Lite($options);

// Test if thereis a valide cache for this id
if ($data = $Cache_Lite->get($id)) {

    // Cache hit !
    // Content is in $data
    // (...)
}
else { // No valid cache found (you have to make the page)

    // Cache miss !
    // Put in $data datas to put in cache
    // (...)
    $data = "Cache_Lite with proper locking@". time();
    $Cache_Lite->save($data);

}

print $data;

