<?php

/**
 * @file
 * Contains \Devour\Tests\Map\NoopMapTest.
 */

namespace Devour\Tests\Map;

use Devour\Map\NoopMap;
use Devour\Tests\DevourTestCase;

class NoopMapTest extends DevourTestCase {

  public function testMap() {

    $map = new NoopMap();

    $this->assertSame('a', $map->getTargetField('a'));
    $this->assertSame('a', $map->getSourceField('a'));
  }

}
