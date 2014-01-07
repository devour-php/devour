<?php

/**
 * @file
 * Contains \Devour\Row\DynamicRow.
 */

namespace Devour\Row;

/**
 * A row class that can be used when the parser fields are unkown or dynamic.
 *
 * @see \Devour\Parser\Csv::parse()
 */
class DynamicRow extends RowBase {

  protected $data;

  public function __construct(array $data) {
    $this->data = $data;
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

  public function getData() {
    return $this->data;
  }

}
