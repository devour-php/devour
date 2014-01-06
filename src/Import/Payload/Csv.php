<?php

/**
 * @file
 * Contains \Import\Payload\Csv.
 */

namespace Import\Payload;

use Import\Row\Row;

class Csv implements ParsedPayloadInterface {

  protected $header;

  protected $rows = array();

  public function setHeader(array $header) {
    $this->header = $header;
  }

  public function addRow(array $row) {
    if ($this->header) {
      $row = array_combine($this->header, $row);
    }

    $this->rows[] = $row;
  }

  public function getRows() {
    return $this->rows;
  }

  public function shiftRow() {
    return new Row(array_shift($this->rows));
  }

}
