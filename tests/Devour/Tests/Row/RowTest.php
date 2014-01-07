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

    $row->set('exists', 1234);
    $this->assertSame(1234, $row->get('exists'));

    $row->setData(array('beep', 'boop'));
    $this->assertSame(array('beep', 'boop'), $row->getData());
  }
}
