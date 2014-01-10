<?php

/**
 * @file
 * Contains \Devour\Tests\Row\RowTest.
 */

namespace Devour\Tests\Row;

use Devour\Row\Row;
use Devour\Table\Table;
use Devour\Tests\DevourTestCase;

/**
 * @covers \Devour\Row\Row
 */
class RowTest extends DevourTestCase {

  protected function getMockTable($field, $value) {
    $table = $this->getMock('Devour\Table\TableInterface');

    $table->expects($this->once())
      ->method('getField')
      ->with($this->equalTo($field))
      ->will($this->returnValue($value));

    return $table;
  }

  public function testRow() {
    $row = new Row($this->getMockTable('does not exist', 9876));

    $this->assertSame(9876, $row->get('does not exist'));

    // Test get, set and fluidity at once.
    $this->assertSame(1234, $row->set('exists', 1234)->get('exists'));

    $this->assertSame(array('beep', 'boop'), $row->setData(array('beep', 'boop'))->getData());
  }

}
