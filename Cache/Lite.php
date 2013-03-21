<?php

require_once "SafeResourceAccess.class.php";

// drop in Cache_Lite base class implementing get/save
class Cache_Lite {

  private $canSave = false;
  public $fp_out;
  private $safe;
  private $opt;

  //
  function save ($data=null) {

    if ($this->canSave) { $this->canSave = false; }
    else                { return false; }

    if (isset($data)) fwrite(
      $this->fp_out,
      $this->opt['automaticSerialization'] ? serialize($data) : $data
    );
    fclose($this->fp_out);

    $safe = $this->safe;

    $safe->waitForWriting();
    // file_put_contents($safe->file, $data);
    rename($safe->temp_file, $safe->file);
    $safe->doneWriting();

    return $safe->finish();
  }

  //
  function get ($id, $group = 'default') {

    $file = $this->opt['cacheDir'] . "cached_". md5("$id|$group");

    $safe = $this->safe = new SafeResourceAccess($file, array(
      "cacheTTL" => $this->opt['lifeTime'],
      "work_dir" => $this->opt['cacheDir'] . "safe_resource_access",
    ));

    // $safe = $this->safe;
    $safe->publisher = $safe->getExclusive();

    if ($safe->publisher) {

      if ( $safe->hasResourceExpired() ) {
        $this->fp_out = $safe->get_temp_fp();
        $this->fp_out && $this->canSave = true;
        return false;
      }
      // do reading..
      // $this->doneExclusive();
    }
    else {
      if ( $safe->hasResourceExpired() ) $safe->waitForRefresh();
      $safe->waitForReading();
      // do reading..
      // $this->doneReading();
    }

    $ret = file_get_contents($safe->file);
    $safe->finish();

    return $this->opt['automaticSerialization'] ? unserialize($ret) : $ret;
  }

    /**
    * Constructor
    *}
    * $options is an assoc. Available options are :
    * $options = array(
    * 'cacheDir' => directory where to put the cache files (string),
    * 'lifeTime' => cache lifetime in seconds (int),
    * 'automaticSerialization' => enable / disable automatic serialization (boolean),

    vvv--- not implemented or fixed values ---vvv

    * 'caching' => enable / disable caching (boolean),
    * 'fileLocking' => enable / disable fileLocking (boolean),
    * 'writeControl' => enable / disable write control (boolean),
    * 'readControl' => enable / disable read control (boolean),
    * 'readControlType' => type of read control 'crc32', 'md5', 'strlen' (string),
    * 'pearErrorMode' => pear error mode (when raiseError is called) (cf PEAR doc) (int),
    * 'memoryCaching' => enable / disable memory caching (boolean),
    * 'onlyMemoryCaching' => enable / disable only memory caching (boolean),
    * 'memoryCachingLimit' => max nbr of records to store into memory caching (int),
    * 'fileNameProtection' => enable / disable automatic file name protection (boolean),
    * 'automaticCleaningFactor' => distable / tune automatic cleaning process (int),
    * 'hashedDirectoryLevel' => level of the hashed directory system (int),
    * 'hashedDirectoryUmask' => umask for hashed directory structure (int),
    * 'errorHandlingAPIBreak' => API break for better error handling ? (boolean)
    * );
    *
    * If sys_get_temp_dir() is available and the
    * 'cacheDir' option is not provided in the
    * constructor options array its output is used
    * to determine the suitable temporary directory.
    *
    * @see http://de.php.net/sys_get_temp_dir
    * @see http://pear.php.net/bugs/bug.php?id=18328
    *
    * @param array $options options
    * @access public
    */
    function __construct ($options = array()) {

        $this->opt = $options + array(
          "lifeTime" => 3600, 
        );

        if (!isset($options['cacheDir']) && function_exists('sys_get_temp_dir')) {

          $this->opt['cacheDir'] = sys_get_temp_dir() . DIRECTORY_SEPARATOR;
        }

    }


}

