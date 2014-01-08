<?php

/**
 * @file
 * Contains \Devour\Tests\Parser\ParserBaseTest.
 */

namespace Devour\Tests\Parser;

use Devour\Table\TableFactory;
use Devour\Tests\DevourTestCase;

/**
 * @covers \Devour\Parser\ParserBase
 */
class ParserBaseTest extends DevourTestCase {

  public function testParserBase() {
    $parser = $this->getMockForAbstractClass('Devour\Parser\ParserBase');

    // Test default factory.
    $this->assertInstanceOf('Devour\Table\TableFactory', $parser->getTableFactory());

    $factory = new TableFactory();
    $parser->setTableFactory($factory);

    $this->assertSame($factory, $parser->getTableFactory());
  }

}
