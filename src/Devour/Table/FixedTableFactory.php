<?php

namespace Devour\Table;

use Devour\Map\MapInterface;

class FixedTableFactory implements TableFactoryInterface {

  public function __construct(TableInterface $table) {
    $this->table = $table;
  }

  public function setTableClass($class) {
  }

  public function create() {
    return $this->table;
  }

  public function setMap(MapInterface $map) {
  }

  public function getMap() {
    return $this->table->getMap();
  }

}
