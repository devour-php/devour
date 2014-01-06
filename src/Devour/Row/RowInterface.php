<?php

/**
 * @file
 * Contains \Devour\Row\RowInterface
 */

namespace Devour\Row;

/**
 * The interface for a single row in a table.
 */
interface RowInterface {

  /**
   * Returns the value for a target field.
   *
   * @param string $target_field
   *   The name of the target field.
   *
   * @return mixed
   *   The value that corresponds to this field.
   */
  public function get($target_field);

}
