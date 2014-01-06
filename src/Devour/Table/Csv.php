<?php

/**
 * @file
 * Contains \Devour\Table\Csv.
 */

namespace Devour\Table;

class Csv extends Table {

  /**
   * The list of header names.
   *
   * @var array
   */
  protected $header;

  /**
   * Sets the header.
   *
   * @param array $header
   *   A list of header names.
   */
  public function setHeader(array $header) {
    $this->header = $header;
  }

  /**
   * Adds a row.
   */
  public function addRow(array $row) {
    if ($this->header) {
      $row = array_combine($this->header, $row);
    }

    parent::addRow($row);
  }

}
