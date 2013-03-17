<?php

require "SafeResourceAccess.class.php";


$hasResourceExpired = function($safe) {

  // $safe->waitForReading(); // <-- uncomment this line if reading from $safe->file

  // cache time to live for publisher or readers
  $cacheTTL = $safe->publisher ? 1*60 : 2*60;

  // clearstatcache(); 
  $filemtime = file_exists($safe->file) ? filemtime($safe->file) : 0;

  // $safe->doneReading();    // <-- uncomment this line if reading from $safe->file
  return ($filemtime + $cacheTTL < time());
};

$writeContent = function($safe) {

  file_put_contents($safe->file, "new content@". time() ."\n");
};

// prepare file for safe access
$safe = new SafeResourceAccess("/tmp/file1.txt", $hasResourceExpired, $writeContent);

// include or read content
include $safe->file;

// call finish immidiately after reading
$safe->finish();

