<?php

/**
 * @file
 * Contains \Devour\Row\Row
 */

namespace Devour\Row;

class Row extends \ArrayIterator implements RowInterface {

  /**
   * {@inheritdoc}
   */
  public function get($target_field) {
    return $this->offsetGet($target_field);
  }

}
