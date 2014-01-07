<?php

namespace Devour\Tests\Table;

use Devour\Row\DynamicRow;
use Devour\Table\Table;
use Devour\Tests\DevourTestCase;

/**
 * Simple tests for the base table.
 */
class TableTest extends DevourTestCase {

  protected $table;
  protected $rows;

  public function setUp() {
    $this->table = new Table();
    $this->rows = array(
      array('a1', 'b1', 'c1'),
      array('a2', 'b2', 'c2'),
      array('a3', 'b3', 'c3'),
    );
  }

  public function testTable() {

    // Test adding.
    foreach ($this->rows as $delta => $row) {
      $row = new DynamicRow($row);
      $this->table->addRow($row);
    }

    // Test getRows().
    $rows = $this->table->getRows();
    $this->assertEquals(count($this->rows), count($rows));

    foreach ($this->rows as $delta => $row) {
      $this->assertEquals($row, $rows[$delta]->getArrayCopy());
    }

    // Test shift.
    $this->assertEquals($this->rows[0], $this->table->shiftRow()->getArrayCopy());

    // Test pop.
    $this->assertEquals($this->rows[2], $this->table->popRow()->getArrayCopy());
  }

}
