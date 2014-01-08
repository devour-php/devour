<?php

/**
 * @file
 * Contains \Devour\Parser\ParserBase.
 */

namespace Devour\Parser;

use Devour\Table\TableFactory;

/**
 * A base class for pasers that has helper methods.
 */
abstract class ParserBase implements ParserInterface {

  /**
   * The table factory.
   *
   * @var \Devour\Table\TableFactory
   */
  protected $tableFactory;

  /**
   * {@inheritdoc}
   */
  public function setTableFactory(TableFactory $table_factory) {
    $this->tableFactory = $table_factory;
  }

  /**
   * {@inheritdoc}
   */
  public function getTableFactory() {
    if (!$this->tableFactory) {
      $this->tableFactory = new TableFactory();
    }

    return $this->tableFactory;
  }

}
