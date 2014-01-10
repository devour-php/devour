<?php

/**
 * @file
 * Contains \Devour\Tests\Processor\PrinterTest.
 */

namespace Devour\Tests\Processor;

use Devour\Processor\Printer;
use Devour\Source\Source;
use Devour\Table\Table;
use Devour\Tests\DevourTestCase;

/**
 * @covers \Devour\Processor\Printer
 */
class PrinterTest extends DevourTestCase {

  public function testPrinter() {

    $table = new Table();
    $data = array(
      array('a' => 'a1','b' => 'b1','c' => 'c1'),
      array('a' => 'a2','b' => 'b2','c' => 'c2'),
      array('a' => 'a3','b' => 'b3','c' => 'c3'),
    );

    foreach ($data as $row) {
      $table->getNewRow()->setData($row);
    }

    $output = '';
    foreach ($data as $row) {
      $line = array();
      foreach ($row as $key => $value) {
        $line[] = "$key: $value";
      }
      $output .= implode(', ', $line) . "\n";
    }

    $this->expectOutputString($output);
    $printer = new Printer();
    $printer->process(new Source(NULL), $table);
  }

}
