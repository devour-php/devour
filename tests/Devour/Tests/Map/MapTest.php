<?php

/**
 * @file
 * Contains \Devour\Tests\Map\MapTest.
 */

namespace Devour\Tests\Map;

use Devour\Map\Map;
use Devour\Tests\DevourTestCase;

class MapTest extends DevourTestCase {

  public function testMap() {

    $map_array = array('a' => 'b');
    $map = new Map($map_array);

    $this->assertSame('b', $map->getTargetField('a'));
    $this->assertSame('a', $map->getSourceField('b'));

    $this->assertNull($map->getTargetField('c'));
    $this->assertNull($map->getSourceField('c'));
  }

}
