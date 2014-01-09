<?php

/**
 * @file
 * Contains \Devour\Table\HasTableFactoryInterface.
 */

namespace Devour\Table;

/**
 * Holds a table factory. Used by parsers, and some transports.
 */
interface HasTableFactoryInterface {

  /**
   * Sets the table factory.
   *
   * @param \Devour\Table\TableFactoryInterface $table_factory
   *   The table factory.
   */
  public function setTableFactory(TableFactoryInterface $table_factory);

  /**
   * Returns the table factory.
   *
   * @return \Devour\Table\TableFactoryInterface
   *   The table factory.
   */
  public function getTableFactory();

}
