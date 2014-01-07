<?php

/**
 * @file
 * Contains \Devour\Tests\Row\RowTest.
 */

namespace Devour\Tests\Row;

use Devour\Map\NoopMap;
use Devour\Row\Row;
use Devour\Table\Table;
use Devour\Tests\DevourTestCase;

class RowTest extends DevourTestCase {

  public function testRow() {
    $map = new NoopMap();
    $table = new Table($map);
    $row = new Row($table, $map);

    $this->assertNull($row->get('does not exist'));

    // Test get, set and fluidity at once.
    $this->assertSame(1234, $row->set('exists', 1234)->get('exists'));

    $this->assertSame(array('beep', 'boop'), $row->setData(array('beep', 'boop'))->getData());
  }
}
