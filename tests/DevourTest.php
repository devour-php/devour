<?php

/**
 * @file
 * Contains \Devour\Tests\DevourTest.
 */

namespace Devour\Tests;

use Devour\Devour;
use Devour\Tests\DevourTestCase;

/**
 * @covers \Devour\Devour
 */
class DevourTest extends DevourTestCase {

  public function test() {
    Devour::registerDefaults();
    $this->assertCount(4, Devour::getRegisteredTransporters());
    $this->assertCount(2, Devour::getRegisteredParsers());
    $this->assertCount(3, Devour::getRegisteredProcessors());
  }

}
