<?php

/**
 * @file
 * Contains \Devour\Tests\Parser\EmbeddedParserTest.
 */

namespace Devour\Tests\Parser;

use Devour\Parser\EmbeddedParser;
use Devour\Source\Source;
use Devour\Table\TableFactoryInterface;
use Devour\Tests\DevourTestCase;
use Devour\Tests\Stream\StreamStub;
use Devour\Tests\Table\TableStub;
use GuzzleHttp\Stream\StreamInterface;

/**
 * @covers \Devour\Parser\EmbeddedParser
 */
class EmbeddedParserTest extends DevourTestCase {

  const EMBED_FIELD = 'embedded_field';

  public function testParse() {
    $source = new Source(NULL);
    $stream = new StreamStub();
    $table = $this->getStubTable();
    $text = 'TABLE TEXT';
    $table->setField(static::EMBED_FIELD, $text);

    $primary = $this->getMock('Devour\Parser\ParserInterface');
    $primary->expects($this->once())
            ->method('parse')
            ->with($this->identicalTo($source), $this->identicalTo($stream))
            ->will($this->returnValue($table));


    $secondary = $this->getMock('Devour\Parser\ParserInterface');

    $secondary->expects($this->once())
              ->method('setTableFactory')
              ->with($this->callback(function (TableFactoryInterface $factory) use ($table) {
                return $factory->create() === $table;
              }));

    $secondary->expects($this->once())
              ->method('parse')
              ->with($this->identicalTo($source), $this->callback(function (StreamInterface $stream) use ($text) {
                return $text === (string) $stream;
              }))
              ->will($this->returnValue($table));

    $parser = new EmbeddedParser($primary, $secondary, static::EMBED_FIELD);
    $this->assertSame($table, $parser->parse($source, $stream));
  }

}
