<?php

/**
 * @file
 * Contains \Devour\Tests\Processor\StubProcessor.
 */

namespace Devour\Tests\Processor;

use Devour\Processor\ProcessorInterface;
use Devour\Table\TableInterface;

/**
 * The interface that all processors must implement.
 *
 * @todo
 */
class StubProcessor implements ProcessorInterface {

  /**
   * Processes the results from a parser.
   *
   * @param \Devour\Table\TableInterface $table
   *   The table to process.
   */
  public function process(TableInterface $table) {

  }

}
