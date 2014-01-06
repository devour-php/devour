<?php

/**
 * @file
 * Contains \Devour\Processor\ProcessorBase.
 */

namespace Devour\Processor;

use Devour\Table\TableInterface;
use Devour\Row\RowInterface;

/**
 * A helper processor class.
 */
abstract class ProcessorBase implements ProcessorInterface {

  /**
   * {@inheritdoc}
   */
  public function process(TableInterface $payload) {
    while ($row = $payload->shiftRow()) {
      $this->processRow($row);
    }
  }

  /**
   * Processes a single row.
   *
   * @param \Devour\Row\RowInterface $row
   *   A single row to process.
   */
  abstract protected function processRow(RowInterface $row);

}
