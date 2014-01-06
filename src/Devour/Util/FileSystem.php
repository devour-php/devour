<?php

namespace Devour\Util;

class FileSystem {

  /**
   * Checks that a path is a file and readable.
   *
   * @param string $filename
   *   A path to a file.
   *
   * @return bool
   *   True if the path is a file and readable, false if not.
   */
  public static function checkFile($filename) {
    return is_file($filename) && is_readable($filename);
  }

  /**
   * Checks that a path is a directory and readable.
   *
   * @param string $directory
   *   A path to a directory.
   *
   * @return bool
   *   True if the path is a directory and readable, false if not.
   */
  public static function checkDirectory($directory) {
    return is_dir($directory) && is_readable($directory);
  }

}
