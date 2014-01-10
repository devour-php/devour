<?php

/**
 * @file
 * Contains \Devour\Tests\Processor\ProcessorBaseTest.
 */

namespace Devour\Tests\Processor;

use Devour\Source\Source;
use Devour\Table\Table;
use Devour\Tests\DevourTestCase;

/**
 * @covers \Devour\Processor\ProcessorBase
 */
class ProcessorBaseTest extends DevourTestCase {

  public function testPrinter() {

    $data = array(
      array('a' => 'a1','b' => 'b1','c' => 'c1'),
      array('a' => 'a2','b' => 'b2','c' => 'c2'),
      array('a' => 'a3','b' => 'b3','c' => 'c3'),
    );

    $processor = $this->getMockForAbstractClass('Devour\Processor\ProcessorBase');
    $processor->expects($this->exactly(3))
              ->method('processRow');

    $processor->process(new Source(NULL), $this->getStubTable($data));
  }

}
