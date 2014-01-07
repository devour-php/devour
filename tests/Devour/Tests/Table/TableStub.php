<?php

/**
 * @file
 * Contains \Devour\Tests\Table\TableStub.
 */

namespace Devour\Tests\Table;

use Devour\Map\MapInterface;
use Devour\Row\Row;
use Devour\Row\RowInterface;
use Devour\Table\TableInterface;

/**
 * A stub table implementation.
 */
class TableStub implements TableInterface {

  public function __construct(MapInterface $map) {
  }

  public function setField($field, $value) {
  }

  public function getNewRow() {
  }

  public function addRowData(array $data) {
  }

  /**
   * {@inheritdoc}
   */
  public function shiftRow() {
  }

  /**
   * {@inheritdoc}
   */
  public function popRow() {
  }

  /**
   * {@inheritdoc}
   */
  public function getRows() {
  }

}
