<?php

/**
 * @file
 * Contains \Devour\Table\CsvTable.
 */

namespace Devour\Table;

use Devour\Row\DynamicRow;

class CsvTable implements TableInterface {

  /**
   * The list of header names.
   *
   * @var array
   */
  protected $header;

  protected $rows = array();

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

    $this->rows[] = $row;
  }

  /**
   * {@inheritdoc}
   */
  public function shiftRow() {
    if ($this->rows) {
      return new DynamicRow(array_shift($this->rows));
    }

    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function popRow() {
    if ($this->rows) {
      return new DynamicRow(array_pop($this->rows));
    }

    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function getRows() {
    return array_map(function($row) {
      return new DynamicRow($row);
    }, $this->rows);
  }

}
