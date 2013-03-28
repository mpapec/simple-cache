<?php

require "SafeCache.class.php";


// get non blocking exclusive lock
$safe = new SafeCache("exclusive_lock_id");

if ( $safe->getExclusive() ) {
  print "we have exclusive lock now<br>";

  // ...

  print "releasing the lock<br>";
  $safe->doneExclusive();
}
