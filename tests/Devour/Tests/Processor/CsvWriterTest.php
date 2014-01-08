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

  const FILE_FULL = 'csv_dir/csv_file.csv';

  public function setUp() {
    $this->cleanUpFiles();
    mkdir(static::DIRECTORY);
  }

  public function testCsvWriter() {

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

    $this->assertSame($output, file_get_contents(static::FILE_FULL));
    unlink(static::FILE_FULL);

    // Test no header.
    $output = str_replace("a,b,c\n", '', $output);
    $csv_writer = new CsvWriter(static::DIRECTORY);
    $csv_writer->process($source, $this->getStubTable($data));
    $this->assertSame($output, file_get_contents(static::FILE_FULL));

    // Test append.
    $csv_writer = new CsvWriter(static::DIRECTORY);
    $csv_writer->process($source, $this->getStubTable($data));
    $this->assertSame($output . $output, file_get_contents(static::FILE_FULL));
    unlink(static::FILE_FULL);

    // Test write.
    $csv_writer = new CsvWriter(static::DIRECTORY, NULL, 'w');
    $csv_writer->process($source, $this->getStubTable($data));
    $csv_writer = new CsvWriter(static::DIRECTORY, NULL, 'w');
    $csv_writer->process($source, $this->getStubTable($data));
    $this->assertSame($output, file_get_contents(static::FILE_FULL));
  }

  /**
   * @covers \Devour\Processor\CsvWriter::clear
   */
  public function testClear() {
    touch(static::FILE_FULL);
    $this->assertTrue(file_exists(static::FILE_FULL));
    $csv_writer = new CsvWriter(static::DIRECTORY);
    $csv_writer->clear(new Source(static::FILE));
    $this->assertFalse(file_exists(static::FILE_FULL));
  }

  /**
   * @covers \Devour\Processor\CsvWriter::fromConfiguration
   */
  public function testFromConfiguration() {
    $config = array('directory' => static::DIRECTORY);
    $this->assertSame('Devour\Processor\CsvWriter', get_class(CsvWriter::fromConfiguration($config)));
  }

}
