<?php

/**
 * @file
 * Contains \Import\Processor\ProcessorBase.
 */

namespace Import\Processor;

use Import\Payload\ParsedPayloadInterface;
use Import\Row\RowInterface;

/**
 * A helper processor class.
 */
abstract class ProcessorBase implements ProcessorInterface {

  /**
   * {@inheritdoc}
   */
  public function process(ParsedPayloadInterface $payload) {
    while ($row = $payload->shiftRow()) {
      $this->processRow($row);
    }
  }

  /**
   * Processes a single row.
   *
   * @param \Import\Row\RowInterface $row
   */
  abstract protected function processRow(RowInterface $row);

}
