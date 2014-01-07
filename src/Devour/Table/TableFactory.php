<?php

namespace Devour\Table;

use Devour\Map\MapInterface;

class TableFactory {

  protected $tableClass = '\Devour\Table\Table';

  public function setTableClass($class) {
    $this->tableClass = $class;
  }

  public function create(MapInterface $map) {
    $class = $this->tableClass;
    return new $class($map);
  }

}
