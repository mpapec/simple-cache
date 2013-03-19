<?php

require "SafeResourceAccess.class.php";

// writer function
$writeContent = function($safe) {

  file_put_contents($safe->file, "new content@". time());
};

// prepare file for safe access
$safe = new SafeResourceAccess("/tmp/file1.txt", array(
  "cacheTTL"     => 1*60,          // cache expire in seconds
  "writeContent" => $writeContent, // function to generate new content
));

// print content
$safe->output();

