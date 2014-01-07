<?php

namespace Devour\Tests\Payload;

use Devour\Payload\GuzzlePayload;
use Devour\Tests\DevourTestCase;

class GuzzlePayloadTest extends DevourTestCase {

  protected $returnValue;

  public function setUp() {
    // Generate a random number to check return values for.
    $this->returnValue = (string) mt_rand(0, PHP_INT_MAX);
  }

  protected function getMockResponse() {
    $stream = $this->getMock('\Guzzle\Http\EntityBodyInterface');

    $stream->expects($this->once())
      ->method('getStream')
      ->will($this->returnValue($this->returnValue));

    $stream->expects($this->once())
      ->method('__toString')
      ->will($this->returnValue($this->returnValue));

    $stream->expects($this->once())
      ->method('getSize')
      ->will($this->returnValue($this->returnValue));

    $response = $this->getMockBuilder('\Guzzle\Http\Message\Response')
                     ->disableOriginalConstructor()
                     ->getMock();

    $response->expects($this->exactly(3))
      ->method('getBody')
      ->will($this->returnValue($stream));

    return $response;
  }

  public function testGuzzlePayload() {
    $response = $this->getMockResponse();
    $payload = new GuzzlePayload($response);

    $this->assertSame($this->returnValue, $payload->getStream());
    $this->assertSame($this->returnValue, $payload->getSize());
    $this->assertSame($this->returnValue, $payload->getContents());

  }

}
