<?php

/**
 * @file
 * Contains \Devour\Table\TableInterface.
 */

namespace Devour\Table;

use Devour\Map\MapInterface;
use Devour\Row\RowInterface;

interface TableInterface {

  public function __construct(MapInterface $map);

  public function createRow();

  public function addRow(RowInterface $row);

  public function addRowData(array $data);

  /**
   * Returns the first row, removing it.
   *
   * Whether the row is actually removed is an implementation detaill.
   *
   * @return \Devour\Row\RowInterface
   *   A row object.
   */
  public function shiftRow();

  /**
   * Returns the last row, removing it.
   *
   * Whether the row is actually removed is an implementation detaill.
   *
   * @return \Devour\Row\RowInterface
   *   A row object.
   */
  public function popRow();

  /**
   * Returns all of the rows in an array.
   *
   * @return \Devour\Row\RowInterface[]
   *   A list of rows.
   */
  public function getRows();

}
