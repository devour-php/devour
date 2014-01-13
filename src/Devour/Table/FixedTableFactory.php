<?php

namespace Devour\Table;

use Devour\Map\MapInterface;

class FixedTableFactory implements TableFactoryInterface {

  /**
   * The table object to reuse.
   *
   * @var \Devour\Table\TableInterface
   */
  protected $table;

  /**
   * Constructs a FixedTableFactory object.
   *
   * @param \Devour\Table\TableInterface $table
   *   The table to reuse.
   */
  public function __construct(TableInterface $table) {
    $this->table = $table;
  }

  /**
   * {@inheritdoc}
   */
  public function setTableClass($class) {
  }

  /**
   * {@inheritdoc}
   */
  public function create() {
    return $this->table;
  }

}
