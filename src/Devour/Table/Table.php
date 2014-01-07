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
class Table extends \SplQueue implements TableInterface {

  /**
   * The fields belonging to the table.
   *
   * @var array
   */
  protected $fields = array();

  /**
   * The map for this table.
   *
   * @var \Devour\Map\MapInterface
   */
  protected $map;

  /**
   * Constructs a new Table.
   *
   * @param \Devour\Map\MapInterface $map
   *   The map this table will use.
   */
  public function __construct(MapInterface $map) {
    $this->map = $map;

    // Default to delete to save memory when possible.
    $this->setIteratorMode(\SplDoublyLinkedList::IT_MODE_DELETE);
  }

  /**
   * {@inheritodc}
   */
  public function setField($field, $value) {
    $this->fields[$field] = $value;
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
    $row = new Row($this, $this->map);
    $this->push($row);
    return $row;
  }

}
