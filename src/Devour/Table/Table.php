<?php

/**
 * @file
 * Contains \Devour\Table\Table.
 */

namespace Devour\Table;

use Devour\Row\RowInterface;

/**
 * A simple table implementation.
 */
class Table implements TableInterface {

  protected $rows = array();

  /**
   * Adds a row.
   */
  public function addRow(RowInterface $row) {
    $this->rows[] = $row;
  }

  /**
   * {@inheritdoc}
   */
  public function shiftRow() {
    return array_shift($this->rows);
  }

  /**
   * {@inheritdoc}
   */
  public function popRow() {
    return array_pop($this->rows);
  }

  /**
   * {@inheritdoc}
   */
  public function getRows() {
    return $this->rows;
  }

}
