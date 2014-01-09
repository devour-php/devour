<?php

/**
 * @file
 * Contains \Devour\Tests\Table\HasTableFactoryTraitTest.
 */

namespace Devour\Tests\Table;

use Devour\Table\TableFactory;
use Devour\Tests\DevourTestCase;

/**
 * @covers \Devour\Table\HasTableFactoryTrait
 */
class HasTableFactoryTraitTest extends DevourTestCase {

  public function test() {
    $trait = $this->getObjectForTrait('Devour\Table\HasTableFactoryTrait');

    // Test default factory.
    $this->assertInstanceOf('Devour\Table\TableFactory', $trait->getTableFactory());

    $factory = new TableFactory();
    $trait->setTableFactory($factory);

    $this->assertSame($factory, $trait->getTableFactory());
  }

}
