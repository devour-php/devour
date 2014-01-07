<?php

namespace Devour\Tests\Table;

use Devour\Map\NoopMap;
use Devour\Table\Table;
use Devour\Tests\DevourTestCase;

/**
 * Simple tests for the base table.
 */
class TableTest extends DevourTestCase {

  protected $table;
  protected $rows;

  public function setUp() {
    $this->table = new Table(new NoopMap());
    $this->rows = array(
      array('a1', 'b1', 'c1'),
      array('a2', 'b2', 'c2'),
      array('a3', 'b3', 'c3'),
    );
  }

  public function testTable() {
    // Test fields.
    $this->table->setField('field 1', 1234);
    $this->assertSame(1234, $this->table->getField('field 1'));
    $this->assertNull($this->table->getField('field does not exist'));

    // Test adding.
    foreach ($this->rows as $row) {
      $this->table->getNewRow()->setData($row);
    }

    // Test Countable interface.
    $this->assertSame(count($this->rows), count($this->table));

    // Test ArrayAccess interface.
    foreach ($this->rows as $delta => $row) {
      $this->assertSame($row, $this->table[$delta]->getData());
    }

    // Test shift.
    $this->assertSame($this->rows[0], $this->table->shift()->getData());

    // Test pop.
    $this->assertSame($this->rows[2], $this->table->pop()->getData());
  }

  public function testTableIteration() {
    foreach ($this->rows as $row) {
      $this->table->getNewRow()->setData($row);
    }

    // Test Iterable interface.
    foreach ($this->table as $row) {
      $this->assertSame(array_shift($this->rows), $row->getData());
    }

    $this->assertTrue($this->table->isEmpty());
  }

}
