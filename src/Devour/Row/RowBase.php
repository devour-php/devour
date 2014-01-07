<?php

/**
 * @file
 * Contains \Devour\Row\RowBase.
 */

namespace Devour\Row;

use Devour\Map\MapInterface;
use Devour\Table\TableInterface;

abstract class RowBase implements RowInterface {

  protected $map;

  protected $table;

  public function setTable(TableInterface $table) {
    $this->table = $table;
  }

  public function setMap(MapInterface $map) {
    $this->map = $map;
  }
}
