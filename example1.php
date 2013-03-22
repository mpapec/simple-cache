<?php

require "SafeResourceAccess.class.php";

//
$newContent = function($safe) {

  // content going to $safe->file
  return "new content@". time();
};


// prepare file for safe access
$safe = new SafeResourceAccess("/tmp/file1.txt", array(
  "cacheTTL"   => 1*60,         // cache expire in seconds
  "newContent" => $newContent, // function to generate new content
));

// print cached content
print $safe->get();

