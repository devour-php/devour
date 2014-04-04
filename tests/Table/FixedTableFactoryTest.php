<?php

/**
 * @file
 * Contains \Devour\Tests\Table\FixedTableFactoryTest.
 */

namespace Devour\Tests\Table;

use Devour\Table\FixedTableFactory;
use Devour\Tests\DevourTestCase;
use Devour\Tests\Table\TableStub;

/**
 * @covers \Devour\Table\FixedTableFactory
 */
class FixedTableFactoryTest extends DevourTestCase {

  public function testTableFactory() {
    $table = new TableStub();
    $factory = new FixedTableFactory($table);

    $this->assertSame($table, $factory->create());
    $factory->setTableClass('beep');
    $this->assertSame($table, $factory->create());
  }

}
