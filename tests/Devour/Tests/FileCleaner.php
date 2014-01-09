<?php

namespace Devour\Tests;

class FileCleaner implements \PHPUnit_Framework_TestListener {
  public function addError(\PHPUnit_Framework_Test $test, \Exception $e, $time) {
  }

  public function addFailure(\PHPUnit_Framework_Test $test, \PHPUnit_Framework_AssertionFailedError $e, $time) {
  }

  public function addIncompleteTest(\PHPUnit_Framework_Test $test, \Exception $e, $time) {
  }

  public function addSkippedTest(\PHPUnit_Framework_Test $test, \Exception $e, $time) {
  }

  public function startTest(\PHPUnit_Framework_Test $test) {
    $this->cleanUpFiles($test);
  }

  public function endTest(\PHPUnit_Framework_Test $test, $time) {
    $this->cleanUpFiles($test);
  }

  public function startTestSuite(\PHPUnit_Framework_TestSuite $suite) {
  }

  public function endTestSuite(\PHPUnit_Framework_TestSuite $suite) {
  }

  /**
   * Removes files and directories based on class constants.
   */
  protected static function cleanUpFiles(\PHPUnit_Framework_Test $test) {

    $refl = new \ReflectionClass(get_class($test));
    $files = array_flip($refl->getConstants());
    $files = array_filter($files, function ($constant_name) {
      return strpos($constant_name, 'FILE') === 0 || strpos($constant_name, 'DIRECTORY') === 0;
    });

    // Remove files first so directories will be empty.
    foreach ($files as $file => $name) {
      if (is_file($file)) {
        unset($files[$name]);
        unlink($file);
      }
    }

    foreach (array_keys($files) as $directory) {
      if (is_dir($directory)) {
        rmdir($directory);
      }
    }
  }
}
