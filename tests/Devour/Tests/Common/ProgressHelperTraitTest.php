<?php

/**
 * @file
 * Contains \Devour\Tests\Common\ProgressHelperTraitTest.
 */

namespace Devour\Tests\Common;

use Devour\Common\ProgressInterface;
use Devour\Source\Source;
use Devour\Tests\DevourTestCase;

/**
 * @covers \Devour\Common\ProgressHelperTrait
 */
class ProgressHelperTraitTest extends DevourTestCase {

  public function test() {
    $trait = $this->getObjectForTrait('Devour\Common\ProgressHelperTrait');
    $this->assertSame(ProgressInterface::COMPLETE, $trait->progress(new Source(NULL)));
    $trait->setProcessLimit(10);
  }

}
