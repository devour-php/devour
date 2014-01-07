<?php

namespace Devour\Tests\Transporter;

use Devour\Source\SourceInterface;
use Devour\Tests\DevourTestCase;
use Devour\Transporter\File;
use Devour\Transporter\Guzzle;

class GuzzleTest extends DevourTestCase {

  public function setUp() {
  }

  public function tearDown() {
  }

  protected function getMockSource($filepath) {
    $source = $this->getMock('\Devour\Source\SourceInterface');

    $source->expects($this->any())
      ->method('getSource')
      ->will($this->returnValue($filepath));

    return $source;
  }

  public function testGuzzle() {
    $transporter = new Guzzle();
    $source = $this->getMockSource('asdfsadf');
    $transporter->getRawPayload($source);
  }

}
