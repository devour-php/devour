<?php

/**
 * @file
 * Contains \Import\Payload\ParsedPayload.
 */

namespace Import\Payload;

use Import\Row\Row;

class ParsedPayload implements ParsedPayloadInterface {

  protected $rows = array();

  public function addRow(array $row) {
    $this->rows[] = $row;
  }

  public function getRows() {
    return $this->rows;
  }

  public function shiftRow() {
    if ($this->rows) {
      return new Row(array_shift($this->rows));
    }

    return FALSE;
  }

}
