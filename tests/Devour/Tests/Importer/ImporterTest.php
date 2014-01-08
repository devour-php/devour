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

  protected $transporter;

  protected $payload;

  protected $parser;

  protected $processor;

  protected $source;

  public function setUp() {
    $this->source = new Source(NULL);
    $this->payload = new FilePayload(NULL);
    $table = $this->getStubTable();

    $this->transporter = $this->getMock('Devour\Transporter\TransporterInterface');
    $this->transporter->expects($this->exactly(2))
                      ->method('transport')
                      ->with($this->source)
                      ->will($this->returnValue($this->payload));

    $this->parser = $this->getMock('Devour\Parser\ParserInterface');
    $this->parser->expects($this->once())
                 ->method('parse')
                 ->with($this->payload)
                 ->will($this->returnValue($table));


    $this->processor = $this->getMock('Devour\Processor\ProcessorInterface');
    $this->processor->expects($this->once())
                    ->method('process')
                    ->with($this->source, $table);
  }

  public function testImporterImport() {
    $importer = new Importer($this->transporter, $this->parser, $this->processor);
    $importer->import($this->source);
    $this->assertSame($this->payload, $importer->transport($this->source));
  }

}
