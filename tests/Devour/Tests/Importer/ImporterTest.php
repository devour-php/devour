<?php

/**
 * @file
 * Contains \Devour\Tests\Importer\ImporterTest.
 */

namespace Devour\Tests\Importer;

use Devour\Importer\Importer;
use Devour\Payload\FilePayload;
use Devour\Source\Source;
use Devour\Tests\DevourTestCase;

/**
 * @covers \Devour\Importer\Importer
 */
class ImporterTest extends DevourTestCase {

  public function testImporterImport() {
    $source = new Source(NULL);
    $payload = new FilePayload(NULL);
    $table = $this->getStubTable();

    $transporter = $this->getMock('Devour\Transporter\TransporterInterface');
    $transporter->expects($this->exactly(2))
                ->method('transport')
                ->with($this->identicalTo($source))
                ->will($this->returnValue($payload));

    $parser = $this->getMock('Devour\Parser\ParserInterface');
    $parser->expects($this->once())
           ->method('parse')
           ->with($this->identicalTo($source), $this->identicalTo($payload))
           ->will($this->returnValue($table));


    $processor = $this->getMock('Devour\Processor\ProcessorInterface');
    $processor->expects($this->once())
              ->method('process')
              ->with($this->identicalTo($source), $this->identicalTo($table));

    $importer = new Importer($transporter, $parser, $processor);
    $importer->import($source);
    $this->assertSame($payload, $importer->transport($source));
  }

  /**
   * @covers \Devour\Importer\Importer::clear
   * @depends testImporterImport
   */
  public function testImporterClear() {
    $source = new Source(NULL);

    $transporter = $this->getMock('Devour\Transporter\TransporterInterface');
    $transporter->expects($this->never())
                ->method('clear');

    $parser = $this->getMock('Devour\Parser\ParserInterface');
    $parser->expects($this->never())
           ->method('clear');

    // Implements ClearableInterface.
    $processor = $this->getMockBuilder('Devour\Processor\CsvWriter')
                      ->disableOriginalConstructor()
                      ->getMock();
    $processor->expects($this->once())
              ->method('clear')
              ->with($this->identicalTo($source));

    $importer = new Importer($transporter, $parser, $processor);
    $importer->clear($source);
  }
}
