<?php

/**
 * @file
 * Contains \Devour\Tests\DevourTestCase.
 */

namespace Devour\Tests;

use Devour\Table\Table;

/**
 * Base testcase class for all Import testcases.
 */
abstract class DevourTestCase extends \PHPUnit_Framework_TestCase {

  protected function getStubTable(array $rows = array()) {
    $table = new Table();
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

  protected static function getProperty($class, $name) {
    $class = new \ReflectionClass($class);
    $property = $class->getProperty($name);
    $property->setAccessible(TRUE);
    return $property;
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

  protected function getMockLogger() {
    return $this->getMock('Psr\Log\LoggerInterface');
  }

}
