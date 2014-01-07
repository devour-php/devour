<?php

/**
 * @file
 * Contains \Devour\Table\Table.
 */

namespace Devour\Table;

use Devour\Map\MapInterface;
use Devour\Row\Row;
use Devour\Row\RowInterface;

/**
 * A simple table implementation.
 */
class Table implements TableInterface {

  protected $data = array();

  protected $rows = array();

  protected $map;

  public function __construct(MapInterface $map) {
    $this->map = $map;
  }

  public function setField($field, $value) {
    $this->data[$field] = $value;
  }

  public function getNewRow() {
    $row = new Row($this, $this->map);
    $this->rows[] = $row;
    return $row;
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
