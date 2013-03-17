<?php

/*

 */
class SafeResourceAccess {

  static $work_dir = "/tmp/safe_resource_access";

  private $_WLock;
  private $_RLock;

  public $publisher;
  public $file; // string id

  private $hash_id;
  private $hasResourceExpiredFunc;
  private $writeContentFunc;

  function __construct ($id, $hasResourceExpiredFunc=null, $writeContentFunc=null) {
    $this->file = $id;
    $this->hash_id = md5($id);
    $this->hasResourceExpiredFunc = $hasResourceExpiredFunc;
    $this->writeContentFunc       = $writeContentFunc;

    // mkdir recursive
    if (! is_dir(self::$work_dir) ) mkdir(self::$work_dir, 0755, true); 

    $this->_main();
  }

  // call user func or inherit this method
  function hasResourceExpired () {
    $func = $this->hasResourceExpiredFunc;

    return $func ? $func($this) : false;
  }
  // call user func or inherit this method
  function writeContent () {
    $func = $this->writeContentFunc;

    return $func ? $func($this) : true;
  }

  //
  function _main () {

    $this->publisher = $this->getExclusive();

    if ($this->publisher) {

      if ( $this->hasResourceExpired() ) {
        // generate new content
        $this->waitForWriting();
        $this->writeContent();
        $this->doneWriting();
      }
      // do reading..
      // else { }

      // $this->doneExclusive();
    }
    else {
      if ( $this->hasResourceExpired() ) $this->waitForRefresh();
      $this->waitForReading();

      // do reading..

      // $this->doneReading();
    }
  }

  // manually release locks
  function finish () {
    return $this->publisher ? $this->doneExclusive() : $this->doneReading();
  }

  // acquire ex. Wlock; nonblocking, return true|false
  function getExclusive() {

    return $this->getWLock($block=0);
  }

  // acquire ex. Rlock
  function waitForWriting () {

    return $this->getRLock($exclusive=1);
  }

  // release ex. Rlock
  function doneWriting () {

    return $this->doneReading();
  }
  // release sh. Rlock
  function doneReading () {

    $ok = $this->releaseLock($this->_RLock);
    unset($this->_RLock);
    return $ok;
  }

  // acquire and release ex Wlock
  function waitForRefresh () {
    $ok = $this->getWLock($block=1) && $this->doneExclusive();
    return $ok;
  }

  // acquire sh. Rlock
  function waitForReading () {

    return $this->getRLock($exclusive=0);
  }

  // release ex. Wlock
  function doneExclusive () {

    $ok = $this->releaseLock($this->_WLock);
    unset($this->_WLock);
    return $ok;
  }


  //
  function releaseLock($fp) {

    if (!$fp) return;
    $ok = flock($fp, LOCK_UN);
    // fclose alone does unlock
    $ok = fclose($fp);

    return $ok;
  }

  // write lock is always exclusive (only my thread and nobody else)
  function getWLock($block) {
    $lock = $block ? LOCK_EX : LOCK_EX|LOCK_NB;

    $lfile = self::$work_dir ."/". $this->hash_id .".wlock";

    $fp = fopen($lfile, "wb");
    if (!$fp) return false;

    // get exclusive lock
    $ok = flock($fp, $lock);
    if ($ok) { $this->_WLock = $fp; } else { fclose($fp); }

    return $ok;
  }

  // read lock is always blocking (always waiting for lock)
  function getRLock($exclusive) {
    $lock = $exclusive ? LOCK_EX : LOCK_SH;

    $lfile = self::$work_dir ."/". $this->hash_id .".rlock";

    $fp = fopen($lfile, "wb");
    if (!$fp) return false;

    // get shared (read) or ex. lock
    $ok = flock($fp, $lock);
    if ($ok) { $this->_RLock = $fp; } else { fclose($fp); }

    return $ok;
  }

}

