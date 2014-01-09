<?php

namespace Devour\Table;

use Devour\Map\MapInterface;

interface TableFactoryInterface {

  public function setTableClass($class);

  public function create();

  public function setMap(MapInterface $map);

  public function getMap();

}
