<?php

namespace Devour\Table;

use Devour\Map\MapInterface;
use Devour\Map\NoopMap;

class TableFactory {

  protected $tableClass = '\Devour\Table\Table';

  protected $map;

  public function setTableClass($class) {
    if (!is_subclass_of($class, '\Devour\Table\TableInterface')) {
      throw new \InvalidArgumentException(sprintf('Class "%s" needs to implement \Devour\Table\TableInterface', $class));
    }
    $this->tableClass = $class;
  }

  public function create() {
    $class = $this->tableClass;
    return new $class($this->getMap());
  }

  public function setMap(MapInterface $map) {
    $this->map = $map;
  }

  public function getMap() {
    if (!$this->map) {
      $this->map = new NoopMap();
    }

    return $this->map;
  }

}
