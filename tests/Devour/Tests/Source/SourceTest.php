<?php

/**
 * @file
 * Contains \Devour\Tests\Source\SourceTest.
 */

namespace Devour\Tests\Source;

use Devour\Source\Source;
use Devour\Tests\DevourTestCase;

/**
 * @covers \Devour\Source\Source
 */
class SourceTest extends DevourTestCase {

  public function testSource() {
    $source = new Source('A');
    $this->assertEquals('A', $source->getSource());
    $this->assertEquals('A', (string) $source);

    $client = new \stdClass();
    $state = $source->getState($client);
    $this->assertInstanceOf('Devour\Common\State', $state);

    $state->bob = 3;

    $this->assertSame(3, $source->getState($client)->bob);
  }

}
