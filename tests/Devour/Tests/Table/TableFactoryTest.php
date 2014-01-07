<?php

/**
 * @file
 * Contains \Devour\Tests\Table\TableFactoryTest.
 */

namespace Devour\Tests\Table;

use Devour\Map\NoopMap;
use Devour\Table\TableFactory;
use Devour\Tests\DevourTestCase;

class TableFactoryTest extends DevourTestCase {

  public function testTableFactory() {
    $stub_class = 'Devour\Tests\Table\TableStub';
    $factory = new TableFactory();

    $factory->setTableClass($stub_class);
    $table = $factory->create(new NoopMap());

    $this->assertSame($stub_class, get_class($table));
  }

  /**
   * @expectedException \InvalidArgumentException
   * @expectedExceptionMessage Class "\Devour\Map\NoopMap" needs to implement \Devour\Table\TableInterface
   */
  public function testTableFactoryException() {
    $stub_class = '\Devour\Map\NoopMap';
    $factory = new TableFactory();
    $factory->setTableClass($stub_class);
  }

}
