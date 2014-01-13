<?php

/**
 * @file
 * Contains \Devour\Row\RowInterface.
 */

namespace Devour\Row;

use Devour\Table\TableInterface;

/**
 * The interface for a single row in a table.
 */
interface RowInterface {

  /**
   * Constructs a RowInterface object.
   *
   * @param \Devour\Table\TableInterface $table
   *   The table the row belongs to.
   */
  public function __construct(TableInterface $table);

  /**
   * Returns the value for a target field.
   *
   * @param string $field
   *   The name of the field.
   *
   * @return mixed|null
   *   The value that corresponds to this field, or null if it does not exist.
   */
  public function get($field);

  /**
   * Sets a value for a field.
   *
   * @param string $field
   *   The name of the field.
   * @param mixed $value
   *   The value for the field.
   *
   * @return self
   *   The row for chaining.
   */
  public function set($field, $value);

  /**
   * Returns the entire internal data array.
   *
   * @return array
   *   The internal data array.
   */
  public function getData();

  /**
   * Sets the data array.
   *
   * @param array $data
   *   The array to use for the row data.
   *
   * @return self
   *   The row for method chaining.
   */
  public function setData(array $data);

}
