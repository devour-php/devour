<?php

/**
 * @file
 * Contains \Devour\Row\Row.
 */

namespace Devour\Row;

use Devour\Table\TableInterface;

class Row implements RowInterface {

  protected $table;

  protected $data = array();

  /**
   * {@inheritdoc}
   */
  public function __construct(TableInterface $table) {
    $this->table = $table;
  }

  /**
   * {@inheritdoc}
   */
  public function get($field) {
    if (isset($this->data[$field])) {
      return $this->data[$field];
    }

    return $this->table->getField($field);
  }

  /**
   * {@inheritdoc}
   */
  public function set($field, $value) {
    $this->data[$field] = $value;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
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
