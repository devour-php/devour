<?php

/**
 * @file
 * Contains \Devour\Processor\ProcessorBase.
 */

namespace Devour\Processor;

use Devour\Row\RowInterface;
use Devour\Source\SourceInterface;
use Devour\Table\TableInterface;

/**
 * A helper processor class.
 */
abstract class ProcessorBase implements ProcessorInterface {

  /**
   * {@inheritdoc}
   */
  public function process(SourceInterface $source, TableInterface $table) {
    foreach ($table as $row) {
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
