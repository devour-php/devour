<?php

/**
 * @file
 * Contains \Devour\Tests\Map\MapTest.
 */

namespace Devour\Tests\Map;

use Devour\Map\Map;
use Devour\Tests\DevourTestCase;

/**
 * @covers \Devour\Map\Map
 */
class MapTest extends DevourTestCase {

  public function testMap() {
    $map_array = [
      ['a', '1'],
      ['b', '2'],
      ['c', '3'],
    ];

    $map = Map::fromConfiguration($map_array);
    $row = 0;
    foreach ($map as $source => $target) {
      $this->assertSame($map_array[$row][0], $source);
      $this->assertSame($map_array[$row][1], $target);
      $row++;
    }
  }

}
