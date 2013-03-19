<?php

require "SafeResourceAccess.class.php";

// new content function
$newContent = function($safe) {

  return $safe->file ."=> new content@". time();
};

// prepare file for safe access
$safe = new SafeResourceAccess("/tmp/file1.txt", array(
  "cacheTTL"   => 1*60,          // cache expire in seconds
  "newContent" => $newContent, // function to generate new content
));

// print content
$safe->output();

