<?php

/**
 * @file
 * Contains \Devour\Table\Table.
 */

namespace Devour\Table;

use Devour\Row\Row;

/**
 * @todo After rename.
 */
class Table implements TableInterface {

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
