<?php

/**
 * @file
 * Contains \Devour\Tests\DevourTestCase.
 */

namespace Devour\Tests;

use Devour\Map\NoopMap;
use Devour\Table\Table;

/**
 * Base testcase class for all Import testcases.
 */
abstract class DevourTestCase extends \PHPUnit_Framework_TestCase {

  protected function getStubTable(array $rows = array()) {
    $map = new NoopMap();
    $table = new Table($map);
    foreach ($rows as $row) {
      $table->getNewRow()->setData($row);
    }

    return $table;
  }

  protected static function getMethod($class, $name) {
    $class = new \ReflectionClass($class);
    $method = $class->getMethod($name);
    $method->setAccessible(TRUE);
    return $method;
  }

  protected function getMockProcess($is_running, $expects) {
    $process = $this->getMockBuilder('Symfony\Component\Process\Process')
                    ->disableOriginalConstructor()
                    ->getMock();
    $process->expects($expects)
            ->method('isRunning')
            ->will($this->returnValue($is_running));

    return $process;
  }

  /**
   * Removes files and directories.
   *
   * The file, or files to remove. This takes a variable number of paramaters to
   * avoid having to wrap the argument in an array.
   *
   * This will also automatically grab any class constants and check them for
   * files to remove.
   */
  protected static function cleanUpFiles() {

    $refl = new \ReflectionClass(get_called_class());
    $files = array_unique(array_filter(array_merge(func_get_args(), $refl->getConstants())));

    // Remove files first so directories will be empty.
    foreach ($files as $delta => $file) {
      if (is_file($file)) {
        unset($files[$delta]);
        unlink($file);
      }
    }

    foreach ($files as $directory) {
      if (is_dir($directory)) {
        rmdir($directory);
      }
    }
  }

}
