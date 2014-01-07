<?php

/**
 * @file
 * Contains \Devour\Tests\Table\TableFactoryTest.
 */

namespace Devour\Tests\Table;

use Devour\Map\Map;
use Devour\Table\TableFactory;
use Devour\Tests\DevourTestCase;

class TableFactoryTest extends DevourTestCase {

  public function testTableFactory() {
    $stub_class = 'Devour\Tests\Table\TableStub';
    $factory = new TableFactory();

    $factory->setTableClass($stub_class);
    $table = $factory->create();

    $this->assertSame($stub_class, get_class($table));

    // Check Map handling.
    $this->assertSame('Devour\Map\NoopMap', get_class($factory->getMap()));

    $map = new Map(array());
    $factory->setMap($map);
    $this->assertSame($map, $factory->getMap());
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
