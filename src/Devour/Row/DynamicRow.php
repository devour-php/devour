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
class DynamicRow extends \ArrayIterator implements RowInterface {

  /**
   * {@inheritdoc}
   */
  public function get($target_field) {
    return $this->offsetGet($target_field);
  }

}
