<?php

/**
 * @file
 * Contains \Devour\Tests\Processor\StubProcessor.
 */

namespace Devour\Tests\Processor;

use Devour\Processor\ProcessorInterface;
use Devour\Table\TableInterface;

/**
 * A stub processor for mocking.
 */
class StubProcessor implements ProcessorInterface {

  /**
   * {@inheritdoc}
   */
  public function process(TableInterface $table) {

  }

}
