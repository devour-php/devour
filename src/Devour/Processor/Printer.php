<?php

/**
 * @file
 * Contains \Devour\Processor\Printer.
 */

namespace Devour\Processor;

use Devour\Row\RowInterface;

/**
 * A simple processor tht prints each row.
 */
class Printer extends ProcessorBase {

  /**
   * {@inheritdoc}
   */
  protected function processRow(RowInterface $row) {

    $output = array();

    foreach ($row->getData() as $key => $value) {
      $output[] = "$key: $value";
    }

    $output = implode(', ', $output);

    print "$output\n";
  }

}
