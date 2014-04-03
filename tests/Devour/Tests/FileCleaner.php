<?php

/**
 * @file
 * Contains \Devour\Tests\FileCleaner.
 */

namespace Devour\Tests;

/**
 * A test listener that removes files automagically.
 *
 * We might put more stuff here later, but this is enough cleverness for now.
 */
class FileCleaner implements \PHPUnit_Framework_TestListener {

  /**
   * {@inheritdoc}
   */
  public function startTest(\PHPUnit_Framework_Test $test) {
    $this->cleanUpFiles($test);
  }

  /**
   * {@inheritdoc}
   */
  public function endTest(\PHPUnit_Framework_Test $test, $time) {
    $this->cleanUpFiles($test);
  }

  /**
   * Removes files and directories based on class constants.
   */
  protected function cleanUpFiles(\PHPUnit_Framework_Test $test) {

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

  /**
   * {@inheritdoc}
   */
  public function addError(\PHPUnit_Framework_Test $test, \Exception $e, $time) {}

  /**
   * {@inheritdoc}
   */
  public function addFailure(\PHPUnit_Framework_Test $test, \PHPUnit_Framework_AssertionFailedError $e, $time) {}

  /**
   * {@inheritdoc}
   */
  public function addIncompleteTest(\PHPUnit_Framework_Test $test, \Exception $e, $time) {}

  /**
   * {@inheritdoc}
   */
  public function addRiskyTest(PHPUnit_Framework_Test $test, Exception $e, $time) {}

  /**
   * {@inheritdoc}
   */
  public function addSkippedTest(\PHPUnit_Framework_Test $test, \Exception $e, $time) {}

  /**
   * {@inheritdoc}
   */
  public function startTestSuite(\PHPUnit_Framework_TestSuite $suite) {}

  /**
   * {@inheritdoc}
   */
  public function endTestSuite(\PHPUnit_Framework_TestSuite $suite) {}

}
