<?php

namespace Devour\Tests\Source;

use Devour\Source\Source;
use Devour\Tests\DevourTestCase;

/**
 * Simple tests for the base table.
 */
class SourceTest extends DevourTestCase {

  public function testSource() {
    $source = new Source('A');
    $this->assertEquals('A', $source->getSource());
    $this->assertEquals('A', (string) $source);
  }

}
