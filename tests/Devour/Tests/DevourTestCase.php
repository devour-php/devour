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

}
