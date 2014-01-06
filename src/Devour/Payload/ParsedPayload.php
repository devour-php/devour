<?php

/**
 * @file
 * Contains \Devour\Payload\ParsedPayload.
 */

namespace Devour\Payload;

use Devour\Row\Row;

/**
 * @todo After rename.
 */
class ParsedPayload implements ParsedPayloadInterface {

  protected $rows = array();

  /**
   * Adds a row.
   */
  public function addRow(array $row) {
    $this->rows[] = $row;
  }

  /**
   * Gets all rows.
   */
  public function getRows() {
    return $this->rows;
  }

  /**
   * {@inheritdoc}
   */
  public function shiftRow() {
    if ($this->rows) {
      return new Row(array_shift($this->rows));
    }

    return FALSE;
  }

}
