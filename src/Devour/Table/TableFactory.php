<?php

namespace Devour\Table;

class TableFactory implements TableFactoryInterface {

  protected $tableClass = 'Devour\Table\Table';

  public function setTableClass($class) {
    if (!is_subclass_of($class, 'Devour\Table\TableInterface')) {
      throw new \InvalidArgumentException(sprintf('Class "%s" needs to implement \Devour\Table\TableInterface', $class));
    }
    $this->tableClass = $class;
  }

  public function create() {
    $class = $this->tableClass;
    return new $class();
  }

}
