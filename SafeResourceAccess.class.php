<?php

/*

 */
class SafeResourceAccess {

  static $work_dir = "/tmp/safe_resource_access";

  private $_WLock;
  private $_RLock;

  public $publisher;
  public $file; // string id
  public $temp_file;

  private $hash_id;
  private $opt;

  function __construct ($id, $arg=array()) {

    $this->file = $id;
    $this->temp_file = $id .".temp";
    $this->hash_id = md5($id);

    $this->opt = $arg + array(
      "cacheTTL" => 3*60, // sec
      "diffTTL" => 15,
      "clearstatcache" => true,
    );

    // mkdir recursive
    if (! is_dir(self::$work_dir) ) mkdir(self::$work_dir, 0755, true);

  }

  // call user func or inherit this method
  function hasResourceExpired () {
    $func = $this->opt["hasResourceExpired"];

    if ($func) return $func($this);

    $cacheTTL = $this->publisher ? ($this->opt["cacheTTL"] - $this->opt["diffTTL"]) : $this->opt["cacheTTL"];

    if ($this->opt["clearstatcache"]) clearstatcache();
    $filemtime = file_exists($this->file) ? filemtime($this->file) : 0;

    return ($filemtime + $cacheTTL < time());
  }
  // call user func or inherit this method
  function newContent () {
    $func = $this->opt["newContent"];

    if (!$func) return false;

    $fp = fopen($this->temp_file, "wb");
    if (!$fp) return false;

    $content = $func($this, $out);
    if (isset($content)) fwrite($fp, $content);

    return fclose($fp);
  }

  // print static content
  function output () {

    $this->ready();
    $ok = readfile($this->file) !== false;
    $this->finish();

    return $ok;
  }

  // include php file
  function include_php () {

    $this->ready();
    include $this->file;
    $this->finish();
  }

  // get file contents
  function get () {

    $this->ready();
    $ret = file_get_contents($this->file);
    $this->finish();

    return $ret;
  }

  //
  function ready () {

    $this->publisher = $this->getExclusive();

    if ($this->publisher) {

      if ( $this->hasResourceExpired() ) {
        // generate new content in $this->temp_file
        $this->newContent();

        $this->waitForWriting();
        rename($this->temp_file, $this->file);
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

