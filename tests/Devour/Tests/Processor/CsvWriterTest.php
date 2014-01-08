<?php

/**
 * @file
 * Contains \Devour\Tests\Processor\CsvWriterTest.
 */

namespace Devour\Tests\Processor;

use Devour\Processor\CsvWriter;
use Devour\Source\Source;
use Devour\Tests\DevourTestCase;

/**
 * @covers \Devour\Processor\CsvWriter
 */
class CsvWriterTest extends DevourTestCase {

  const DIRECTORY = 'csv_dir';

  const FILE = 'csv_file';

  protected function getFileName() {
    return static::DIRECTORY . '/' . static::FILE . '.csv';
  }

  protected function cleanUp() {
    if (file_exists($this->getFileName())) {
      unlink($this->getFileName());
    }
    if (is_dir(static::DIRECTORY)) {
      rmdir(static::DIRECTORY);
    }
  }

  public function setUp() {
    $this->cleanUp();
    mkdir(static::DIRECTORY);
  }

  public function tearDown() {
    $this->cleanUp();
  }

  public function testPrinter() {

    $source = new Source(static::FILE);

    $data = array(
      array('a' => 'a1','b' => 'b1','c' => 'c1'),
      array('a' => 'a2','b' => 'b2','c' => 'c2'),
      array('a' => 'a3','b' => 'b3','c' => 'c3'),
    );

    $output = array('a,b,c');
    foreach ($data as $row) {
      $output[] = implode(',', $row);
    }
    $output = implode("\n", $output) . "\n";

    $csv_writer = new CsvWriter(static::DIRECTORY, array('a', 'b', 'c'));
    $csv_writer->process($source, $this->getStubTable($data));

    $this->assertSame($output, file_get_contents($this->getFileName()));
    unlink($this->getFileName());

    // Test no header.
    $output = str_replace("a,b,c\n", '', $output);
    $csv_writer = new CsvWriter(static::DIRECTORY);
    $csv_writer->process($source, $this->getStubTable($data));
    $this->assertSame($output, file_get_contents($this->getFileName()));

    // Test append.
    $csv_writer = new CsvWriter(static::DIRECTORY);
    $csv_writer->process($source, $this->getStubTable($data));
    $this->assertSame($output . $output, file_get_contents($this->getFileName()));
    unlink($this->getFileName());

    // Test write.
    $csv_writer = new CsvWriter(static::DIRECTORY, NULL, 'w');
    $csv_writer->process($source, $this->getStubTable($data));
    $csv_writer = new CsvWriter(static::DIRECTORY, NULL, 'w');
    $csv_writer->process($source, $this->getStubTable($data));
    $this->assertSame($output, file_get_contents($this->getFileName()));
  }

  public function testFromConfiguration() {
    $config = array('directory' => static::DIRECTORY);
    $this->assertSame('Devour\Processor\CsvWriter', get_class(CsvWriter::fromConfiguration($config)));
  }

}
