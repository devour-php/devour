<?php

/**
 * @file
 * Contains \Devour\Row\RowInterface
 */

namespace Devour\Row;

use Devour\Map\MapInterface;
use Devour\Table\TableInterface;

/**
 * The interface for a single row in a table.
 */
interface RowInterface {

  public function setTable(TableInterface $table);

  public function setMap(MapInterface $map);

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
