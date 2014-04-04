<?php

/**
 * @file
 * Contains \Devour\Processor\ProcessorInterface.
 */

namespace Devour\Processor;

use Devour\Source\SourceInterface;
use Devour\Table\TableInterface;

/**
 * The interface that all processors must implement.
 *
 * @todo
 */
interface ProcessorInterface {

  /**
   * Processes the results from a parser.
   *
   * @param \Devour\Table\TableInterface $table
   *   The table to process.
   *
   * @return void
   */
  public function process(SourceInterface $source, TableInterface $table);

}
