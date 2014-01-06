<?php

/**
 * @file
 * Contains \Devour\Payload\Csv.
 */

namespace Devour\Payload;

use Devour\Row\Row;

class Csv extends ParsedPayload {

  protected $header;

  public function setHeader(array $header) {
    $this->header = $header;
  }

  public function addRow(array $row) {
    if ($this->header) {
      $row = array_combine($this->header, $row);
    }

    parent::addRow($row);
  }

}
