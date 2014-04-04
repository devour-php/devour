<?php

/**
 * @file
 * Contains \Devour\Tests\Table\TableFactoryTest.
 */

namespace Devour\Tests\Table;

use Devour\Table\TableFactory;
use Devour\Tests\DevourTestCase;

/**
 * @covers \Devour\Table\TableFactory
 */
class TableFactoryTest extends DevourTestCase {

  public function testTableFactory() {
    $stub_class = 'Devour\Tests\Table\TableStub';
    $factory = new TableFactory();

    $factory->setTableClass($stub_class);
    $table = $factory->create();

    $this->assertInstanceOf($stub_class, $table);
  }

  /**
   * @expectedException \InvalidArgumentException
   * @expectedExceptionMessage Class "Devour\Map\Map" needs to implement \Devour\Table\TableInterface
   */
  public function testTableFactoryException() {
    $stub_class = 'Devour\Map\Map';
    $factory = new TableFactory();
    $factory->setTableClass($stub_class);
  }

}
