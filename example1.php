<?php

require "SafeCache.class.php";

//
$newContent = function($safe) {

  // content going to $safe->file
  return "new content@". time();
};


// prepare file for safe access
$safe = new SafeCache("/tmp/file1.txt", array(
  "cacheTTL"   => 1*60,         // cache expire in seconds
  "newContent" => $newContent, // function to generate new content
));

// print cached content
print $safe->get();

