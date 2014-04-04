<?php

/**
 * @file
 * Contains \Devour\Table\HasTableFactoryTrait.
 */

namespace Devour\Table;

use Devour\Table\TableFactory;
use Devour\Table\TableFactoryInterface;

/**
 * Shortcut to implement HasTableFactoryInterface.
 */
trait HasTableFactoryTrait {

  /**
   * The table factory.
   *
   * @var \Devour\Table\TableFactoryInterface
   */
  protected $tableFactory;

  /**
   * Sets the table factory.
   *
   * @param \Devour\Table\TableFactoryInterface $table_factory
   *   The table factory.
   */
  public function setTableFactory(TableFactoryInterface $table_factory) {
    $this->tableFactory = $table_factory;
  }

  /**
   * Returns the table factory.
   *
   * @return \Devour\Table\TableFactoryInterface
   *   The table factory.
   */
  public function getTableFactory() {
    if (!$this->tableFactory) {
      $this->tableFactory = new TableFactory();
    }

    return $this->tableFactory;
  }

}
