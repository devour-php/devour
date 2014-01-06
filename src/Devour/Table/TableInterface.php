<?php

/**
 * @file
 * Contains \Devour\Table\TableInterface.
 */

namespace Devour\Table;

interface TableInterface {

  /**
   * Returns the first row, removing it.
   *
   * @return \Devour\Row\RowInterface.
   *   A row object.
   */
  public function shiftRow();

}
