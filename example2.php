<?php

require "SafeResourceAccess.class.php";
header("Content-Type:text/plain");

//
$newContent = function($safe, $out) {

  // content going to $safe->file
  // direct write with fwrite for large content
  foreach (range(0,20) as $n) fwrite($out, rand(0,9));

  fwrite($out, " writing directly via fwrite <?php print time();?>\n\n");
};


// prepare file for safe access
$safe = new SafeResourceAccess("/tmp/file2.txt", array(
  "cacheTTL"   => 1*60,         // cache expire in seconds
  "newContent" => $newContent, // function to generate new content
));

// include compiled php template
$safe->include_php();

// fastest file output, only for static content
$safe->output();
