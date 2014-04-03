<?php

/**
 * @file
 * Contains \Devour\Tests\Parser\CsvTest.
 */

namespace Devour\Tests\Parser;

use Devour\Common\ProgressInterface;
use Devour\Parser\Csv;
use Devour\Source\Source;
use Devour\Tests\DevourTestCase;
use Devour\Tests\Stream\StreamStub;

/**
 * @covers \Devour\Parser\Csv
 * @todo Test batching.
 */
class CsvTest extends DevourTestCase {

  const FILE_1 = 'file_1';

  protected $csv;
  protected $csvData;

  public function setUp() {
    $this->csv = new Csv();

    $this->csvData = [
      ['one', 'two', 'three'],
      ['a1', 'b1', 'c1'],
      ['a2', 'b2', 'c2'],
      ['a3', 'b3', 'c3'],
    ];

    // Create a copy.
    $csv_data = $this->csvData;

    foreach ($csv_data as &$row) {
      $row = implode(',', $row);
    }

    $this->stringData = implode("\n", $csv_data);

    $this->stream = new StreamStub($this->stringData, TRUE);
  }

  public function testParse() {
    $source = new Source(NULL);
    $this->assertSame(ProgressInterface::COMPLETE, $this->csv->progress($source));

    $result = $this->csv->parse($source, $this->stream);
    $this->assertInstanceOf('Devour\Table\Table', $result);

    // Check that rows were parsed correctly.
    $this->assertSame(count($this->csvData), count($result));
    foreach ($this->csvData as $key => $data) {
      $this->assertSame($data, $result[$key]->getData());
    }

    $this->assertSame(ProgressInterface::COMPLETE, $this->csv->progress($source));

    // Test that an empty array is returned after parsing is complete.
    $result = $this->csv->parse($source, $this->stream);
    $this->assertSame(0, count($result));
  }

  /**
   * @depends testParse
   */
  public function testParseWithHeaders() {
    $source = new Source(NULL);
    $this->csv->setHasHeader(TRUE);

    $result = $this->csv->parse($source, $this->stream);

    // Check that rows were parsed correctly.
    // Remove header line.
    $header = array_shift($this->csvData);

    $this->assertSame(count($this->csvData), count($result));
    foreach ($this->csvData as $key => $row) {
      $this->assertSame(array_combine($header, $row), $result[$key]->getData());
    }
  }

  /**
   * @depends testParse
   */
  public function testLimit() {
    $source = new Source(NULL);
    $this->csv->setProcessLimit(2);

    $result = $this->csv->parse($source, $this->stream);
    $this->assertSame(.8, $this->csv->progress($source));

    // Complete parsing.
    $this->csv->parse($source, $this->stream);
    $this->assertSame(ProgressInterface::COMPLETE, $this->csv->progress($source));
  }

  /**
   * @covers \Devour\Parser\Csv::fromConfiguration
   * @depends testParse
   */
  public function testFactory() {
    $parser = Csv::fromConfiguration(['has_header' => TRUE]);
    $this->assertInstanceOf(get_class($this->csv), $parser);
  }

}
