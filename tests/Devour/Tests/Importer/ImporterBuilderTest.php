<?php

/**
 * @file
 * Contains \Devour\Tests\Importer\ImporterBuilderTest.
 */

namespace Devour\Tests\Importer;

use Devour\Importer\ImporterBuilder;
use Devour\Table\TableFactory;
use Devour\Tests\DevourTestCase;

/**
 * @covers \Devour\Importer\ImporterBuilder
 */
class ImporterBuilderTest extends DevourTestCase {

  /**
   * @expectedException \LogicException
   * @expectedExceptionMessage Builders can only be used once.
   */
  public function testBuilder() {
    $table_factory = $this->getMock('Devour\Table\TableFactoryInterface');

    $processor = $this->getMock('Devour\Processor\ProcessorInterface');

    // Implicitly test command recording.
    $builder = ImporterBuilder::get()
      ->setProcessLimit(10)
      ->setTableFactory($table_factory)
      ->setTransporter('Devour\Transporter\Guzzle')
      ->setParser('Devour\Parser\Csv')
    ->setProcessor($processor);

    $importer = $builder->build();
    $this->assertInstanceOf('Devour\Importer\Importer', $importer);

    $parser = $importer->getParser();
    $this->assertInstanceOf('Devour\Parser\Csv', $parser);
    $this->assertSame($table_factory, $parser->getTableFactory());

    $this->assertSame($processor, $importer->getProcessor());

    // Throws exception.
    $builder->build();
  }

  /**
   * Test that transporters implementing HasTableFactoryInterface do not need
   * parsers.
   */
  public function testNoParser() {
    $transporter = $this->getMockBuilder('Devour\Transporter\Database')
                        ->disableOriginalConstructor()
                        ->getMock();
    $transporter->expects($this->once())
                ->method('setProcessLimit')
                ->with($this->equalTo(10));

    $builder = ImporterBuilder::get()
      ->setProcessLimit(10)
      ->setTransporter($transporter)
      ->setProcessor('Devour\Tests\Processor\ProcessorStub');

    $importer = $builder->build();

    $this->assertSame($transporter, $importer->getTransporter());
  }

  /**
   * @covers \Devour\Importer\ImporterBuilder::setImporter
   */
  public function testSetImporter() {
    $importer = $this->getMock('Devour\Importer\ImporterInterface');
    $importer->expects($this->once())
             ->method('getTransporter')
             ->will($this->returnValue(TRUE));
    $importer->expects($this->once())
             ->method('getParser')
             ->will($this->returnValue(TRUE));
    $importer->expects($this->once())
            ->method('getProcessor')
            ->will($this->returnValue(TRUE));

    $result = ImporterBuilder::get()
      ->setTransporter('Devour\Tests\Transporter\TransporterStub')
      ->setParser('Devour\Tests\Parser\ParserStub')
      ->setProcessor('Devour\Tests\Processor\ProcessorStub')
      ->setImporter($importer)
      ->build();

    $this->assertSame($importer, $result);
  }

  /**
   * @covers \Devour\Importer\ImporterBuilder::setTableClass
   */
  public function testSetTableClass() {
    $importer = ImporterBuilder::get()
      ->setTransporter('Devour\Tests\Transporter\TransporterStub')
      ->setParser('Devour\Tests\Parser\ParserStub')
      ->setProcessor('Devour\Tests\Processor\ProcessorStub')
      ->setTableClass('Devour\Table\Table')
      ->build();
  }

  /**
   * @covers \Devour\Importer\ImporterBuilder::setMap
   */
  public function testSetMap() {
    $map = $this->getMock('Devour\Map\MapInterface');

    $importer = ImporterBuilder::get()
      ->setTransporter('Devour\Tests\Transporter\TransporterStub')
      ->setParser('Devour\Tests\Parser\ParserStub')
      ->setProcessor('Devour\Tests\Processor\ProcessorStub')
      ->setMap($map)
      ->build();

    $this->assertSame($map, $importer->getParser()->getTableFactory()->getMap());
  }

  /**
   * @expectedException \LogicException
   * @expectedExceptionMessage The importer does not have a transporter!
   */
  public function testTransporterValidation() {
    ImporterBuilder::get()->build();
  }

  /**
   * @expectedException \LogicException
   * @expectedExceptionMessage The importer does not have a parser!
   */
  public function testParserValidation() {
    ImporterBuilder::get()
      ->setTransporter($this->getMock('Devour\Transporter\TransporterInterface'))
      ->build();
  }

  /**
   * @expectedException \LogicException
   * @expectedExceptionMessage The importer does not have a processor!
   */
  public function testProcessorValidation() {
    ImporterBuilder::get()
      ->setTransporter($this->getMock('Devour\Transporter\TransporterInterface'))
      ->setParser($this->getMock('Devour\Parser\ParserInterface'))
      ->build();
  }

  /**
   * @expectedException \RuntimeException
   * @expectedExceptionMessage The "IDONOTEXIST" class does not exist.
   */
  public function testInvalidClass() {
    ImporterBuilder::get()->setTransporter('IDONOTEXIST');
  }

}
