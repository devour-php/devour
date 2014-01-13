<?php

/**
 * @file
 * Contains \Devour\Table\TableFactoryInterface.
 */

namespace Devour\Table;

/**
 * The interface for table factories.
 */
interface TableFactoryInterface {

  /**
   * Sets the table class.
   *
   * @param string $class
   *   The table class to create.
   *
   * @return void
   */
  public function setTableClass($class);

  /**
   * Creates a new table class.
   *
   * @return \Devour\Table\TableInterface
   *   A new table.
   */
  public function create();

}
