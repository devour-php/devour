<?php

/**
 * @file
 * Contains \Devour\Table\TableInterface.
 */

namespace Devour\Table;

/**
 * Tables get returned from parsers and passed into processors.
 */
interface TableInterface extends \Iterator, \ArrayAccess, \Countable {

  /**
   * Returns a new row.
   *
   * Rows cannot be created independently from the table, this method must be
   * used.
   *
   * @return \Devour\Row\RowInterface
   *   A new row, bound to the table.
   */
  public function getNewRow();

  /**
   * Sets a field for this table.
   *
   * @param string $field
   *   The name of the field.
   * @param mixed $value
   *   The value of the field.
   *
   * @return self
   *   The table for chaining.
   */
  public function setField($field, $value);

  /**
   * Gets a field from this table.
   *
   * @param string $field
   *   The name of the field.
   *
   * @return mixed|null
   *   The value of the field, or null if it does not exist.
   */
  public function getField($field);

  /**
   * Returns the first row, removing it.
   *
   * Whether the row is actually removed is an implementation detail.
   *
   * @return \Devour\Row\RowInterface
   *   A row object.
   */
  public function shift();

  /**
   * Returns the last row, removing it.
   *
   * Whether the row is actually removed is an implementation detail.
   *
   * @return \Devour\Row\RowInterface
   *   A row object.
   */
  public function pop();

}
