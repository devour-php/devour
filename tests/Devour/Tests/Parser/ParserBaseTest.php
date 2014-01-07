<?php

/**
 * @file
 * Contains \Devour\Tests\Parser\ParserBaseTest.
 */

namespace Devour\Tests\Parser;

use Devour\Table\TableFactory;
use Devour\Tests\DevourTestCase;

class ParserBaseTest extends DevourTestCase {

  public function testParserBase() {
    $parser = $this->getMockForAbstractClass('\Devour\Parser\ParserBase');
    $factory = new TableFactory();
    $parser->setTableFactory($factory);

    $this->assertSame($factory, $parser->getTableFactory());
  }

}
