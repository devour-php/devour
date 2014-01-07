<?php

namespace Devour\Tests\Parser;

use Devour\Parser\Csv;
use Devour\Payload\FilePayload;
use Devour\ProgressInterface;
use Devour\Tests\DevourTestCase;

/**
 * @todo Test batching.
 */
class CsvTest extends DevourTestCase {

  const FILE_1 = './file_1';

  protected $csv;
  protected $csvData;

  public function setUp() {
    $this->csv = new Csv();

    $this->csvData = array(
      array('one', 'two', 'three'),
      array('a1', 'b1', 'c1'),
      array('a2', 'b2', 'c2'),
      array('a3', 'b3', 'c3'),
    );

    // Create a copy.
    $csv_data = $this->csvData;

    foreach ($csv_data as &$row) {
      $row = implode(',', $row);
    }

    $csv_data = implode("\n", $csv_data);
    file_put_contents(static::FILE_1, $csv_data);
  }

  public function tearDown() {
    unlink(static::FILE_1);
  }

  protected function getMockRawPayload($filepath) {
    return new FilePayload($filepath);
  }

  public function testParse() {
    $this->assertEquals(ProgressInterface::COMPLETE, $this->csv->progress());


    $payload = $this->getMockRawPayload(static::FILE_1);
    $result = $this->csv->parse($payload);
    $this->assertInstanceOf('\Devour\Table\Table', $result);

    // Check that rows were parsed correctly.
    $rows = $result->getRows();
    $this->assertEquals(count($this->csvData), count($rows));
    foreach ($this->csvData as $key => $data) {
      $this->assertEquals($data, $rows[$key]->getData());
    }

    $this->assertEquals(ProgressInterface::COMPLETE, $this->csv->progress());

    // Test that an empty array is returned after parsing is complete.
    $payload = $this->getMockRawPayload(static::FILE_1);
    $result = $this->csv->parse($payload);
    $this->assertEquals(0, count($result->getRows()));
  }

  public function testParseWithHeaders() {
    $this->csv->setHasHeader(TRUE);
    $this->assertEquals(ProgressInterface::COMPLETE, $this->csv->progress());


    $payload = $this->getMockRawPayload(static::FILE_1);
    $result = $this->csv->parse($payload);
    $this->assertInstanceOf('\Devour\Table\Table', $result);

    // Check that rows were parsed correctly.
    $rows = $result->getRows();

    // Remove header line.
    $header = array_shift($this->csvData);

    $this->assertEquals(count($this->csvData), count($rows));
    foreach ($this->csvData as $key => $row) {
      $this->assertEquals(array_combine($header, $row), $rows[$key]->getData());
    }

    $this->assertEquals(ProgressInterface::COMPLETE, $this->csv->progress());
  }

  public function testLimit() {
    $this->csv->setLimit(2);

    $payload = $this->getMockRawPayload(static::FILE_1);
    $result = $this->csv->parse($payload);
    $this->assertSame(.8, $this->csv->progress());

    // Complete parsing.
    $this->csv->parse($this->getMockRawPayload(static::FILE_1));
    $this->assertSame(1.0, $this->csv->progress());
  }

}
