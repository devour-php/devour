<?php

/**
 * @file
 * Contains \Devour\Row\Row.
 */

namespace Devour\Row;

use Devour\Map\MapInterface;
use Devour\Table\TableInterface;

class Row implements RowInterface {

  protected $map;

  protected $table;

  protected $data = array();

  /**
   * {@inheritdoc}
   */
  public function __construct(TableInterface $table, MapInterface $map) {
    $this->table = $table;
    $this->map = $map;
  }

  /**
   * {@inheritdoc}
   */
  public function get($target_field) {
    $source_field = $this->map->getSourceField($target_field);
    if (isset($this->data[$source_field])) {
      return $this->data[$source_field];
    }
  }

  /**
   * {@inheritdoc}
   */
  public function set($source_field, $value) {
    $this->data[$source_field] = $value;
    return $this;
  }

  public function setData(array $data) {
    $this->data = $data;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getData() {
    return $this->data;
  }

}
