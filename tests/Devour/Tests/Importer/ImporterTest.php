<?php

/**
 * @file
 * Contains \Devour\Tests\Importer\ImporterTest.
 */

namespace Devour\Tests\Importer;

use Devour\Common\ProgressInterface;
use Devour\Importer\Importer;
use Devour\Source\Source;
use Devour\Tests\DevourTestCase;
use Devour\Tests\Stream\StreamStub;

/**
 * @covers \Devour\Importer\Importer
 */
class ImporterTest extends DevourTestCase {

  public function testImporterImport() {
    $source = new Source(NULL);
    $stream = new StreamStub();
    $table = $this->getStubTable();

    $transporter = $this->getMock('Devour\Transporter\TransporterInterface');
    $transporter->expects($this->exactly(2))
                ->method('transport')
                ->with($this->identicalTo($source))
                ->will($this->returnValue($stream));

    $parser = $this->getMock('Devour\Parser\ParserInterface');
    $parser->expects($this->once())
           ->method('parse')
           ->with($this->identicalTo($source), $this->identicalTo($stream))
           ->will($this->returnValue($table));


    $processor = $this->getMock('Devour\Processor\ProcessorInterface');
    $processor->expects($this->once())
              ->method('process')
              ->with($this->identicalTo($source), $this->identicalTo($table));

    $importer = new Importer();

    $importer->setTransporter($transporter);
    $importer->setParser($parser);
    $importer->setProcessor($processor);

    $logger = $this->getMockLogger();
    $importer->setLogger($logger);
    $this->assertSame($logger, $importer->getLogger());
    $importer->validate();

    $importer->import($source);
    $this->assertSame($stream, $importer->transport($source));
  }

  public function testImportFetcherReturnsTable() {
    $source = new Source(NULL);
    $table = $this->getStubTable();

    $transporter = $this->getMockBuilder('Devour\Transporter\Database')
                        ->disableOriginalConstructor()
                        ->getMock();
    $transporter->expects($this->exactly(1))
                ->method('transport')
                ->with($this->identicalTo($source))
                ->will($this->returnValue($table));
    $transporter->expects($this->exactly(1))
                ->method('progress')
                ->with($this->identicalTo($source))
                ->will($this->returnValue(ProgressInterface::COMPLETE));

    $processor = $this->getMock('Devour\Processor\ProcessorInterface');
    $processor->expects($this->once())
              ->method('process')
              ->with($this->identicalTo($source), $this->identicalTo($table));

    $importer = new Importer();

    $importer->setTransporter($transporter);
    $importer->setProcessor($processor);

    $importer->validate();

    $importer->import($source);
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

    $importer = new Importer();

    $importer->setTransporter($transporter);
    $importer->setParser($parser);
    $importer->setProcessor($processor);

    $importer->clear($source);
  }

  /**
   * @expectedException \DomainException
   * @expectedExceptionMessage returned an invalid value
   */
  public function testImportFetcherReturnsInvalid() {
    $source = new Source(NULL);

    $transporter = $this->getMock('Devour\Transporter\TransporterInterface');
    $transporter->expects($this->exactly(1))
                ->method('transport')
                ->with($this->identicalTo($source))
                ->will($this->returnValue($source));

    $importer = new Importer();

    $importer->setTransporter($transporter);
    $importer->import($source);
  }

  /**
   * @expectedException \DomainException
   * @expectedExceptionMessage The importer does not have a transporter!
   */
  public function testImportValidationTransporter() {
    $importer = new Importer();
    $importer->validate();
  }

  /**
   * @expectedException \DomainException
   * @expectedExceptionMessage The importer does not have a parser!
   */
  public function testImportValidationParser() {
    $importer = new Importer();
    $importer->setTransporter($this->getMock('Devour\Transporter\TransporterInterface'));
    $importer->validate();
  }

  /**
   * @expectedException \DomainException
   * @expectedExceptionMessage The importer does not have a processor!
   */
  public function testImportValidationProcessor() {
    $importer = new Importer();
    $importer->setTransporter($this->getMock('Devour\Transporter\TransporterInterface'));
    $importer->setParser($this->getMock('Devour\Parser\ParserInterface'));
    $importer->validate();
  }
}
