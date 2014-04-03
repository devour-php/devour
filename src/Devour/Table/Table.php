<?php

/**
 * @file
 * Contains \Devour\Table\Table.
 */

namespace Devour\Table;

use Devour\Row\Row;

/**
 * A simple table implementation.
 */
class Table extends \SplQueue implements TableInterface {

  /**
   * The fields belonging to the table.
   *
   * @var array
   */
  protected $fields = [];

  /**
   * Constructs a Table object.
   */
  public function __construct() {
    // Default to delete to save memory when possible. HHVM < 3.0 has a bug
    // regarding setIteratorMode().
    if (!defined('HHVM_VERSION')) {
      $this->setIteratorMode(self::IT_MODE_FIFO | self::IT_MODE_DELETE);
    }
  }

  /**
   * {@inheritodc}
   */
  public function setField($field, $value) {
    $this->fields[$field] = $value;
    return $this;
  }

  /**
   * {@inheritodc}
   */
  public function getField($field) {
    if (isset($this->fields[$field])) {
      return $this->fields[$field];
    }
  }

  /**
   * {@inheritodc}
   */
  public function getNewRow() {
    $row = new Row($this);
    $this->push($row);
    return $row;
  }

}
