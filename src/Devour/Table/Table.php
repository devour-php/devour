<?php

/**
 * @file
 * Contains \Devour\Table\Table.
 */

namespace Devour\Table;

use Devour\Map\MapInterface;
use Devour\Row\RowInterface;

/**
 * A simple table implementation.
 */
class Table implements TableInterface {

  protected $rows = array();

  protected $map;

  public function __construct(MapInterface $map) {
    $this->map = $map;
  }

  /**
   * Adds a row.
   */
  public function addRow(RowInterface $row) {
    $row->setTable($this);
    $row->setMap($this->map);
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
