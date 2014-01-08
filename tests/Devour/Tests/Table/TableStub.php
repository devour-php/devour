<?php

/**
 * @file
 * Contains \Devour\Tests\Table\TableStub.
 */

namespace Devour\Tests\Table;

use Devour\Map\MapInterface;
use Devour\Table\TableInterface;

/**
 * A stub table implementation.
 */
class TableStub extends \SplQueue implements TableInterface {

  public function __construct(MapInterface $map) {
  }

  public function setField($field, $value) {
  }

  public function getField($field) {
  }

  public function getNewRow() {
  }

}
