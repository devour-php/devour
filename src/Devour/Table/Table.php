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

  public function createRow() {
    return new Row($this, $this->map);
  }

  /**
   * Adds a row.
   */
  public function addRow(RowInterface $row) {
    $this->rows[] = $row;
  }

  public function addRowData(array $data) {
    $row = $this->createRow();
    $row->setData($data);
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
