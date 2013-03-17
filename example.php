<?php

require "SafeResourceAccess.class.php";


$hasResourceExpired = function($file, $publisher) {
  // cache time to live
  $cacheTTL = $publisher ? 1*60 : 2*60;

  clearstatcache();
  $filemtime = file_exists($file) ? @filemtime($file) : 0;

  return ($filemtime + $cacheTTL < time());
};

$writeContent = function($file) {

  file_put_contents($file, "new content@". time() ."\n");
};


$safe = new SafeResourceAccess("/tmp/file1.txt", $hasResourceExpired, $writeContent);

// include or read content
include $safe->file;

// call finish immidiately after reading
$safe->finish();

