<?php

/**
 * @file
 * Contains \Devour\Tests\Payload\GuzzlePayloadTest.
 */

namespace Devour\Tests\Payload;

use Devour\Payload\GuzzlePayload;
use Devour\Tests\DevourTestCase;
use Guzzle\Http\EntityBody;

/**
 * @covers \Devour\Payload\GuzzlePayload
 */
class GuzzlePayloadTest extends DevourTestCase {

  protected $returnValue;

  protected $handle;

  public function setUp() {
    // Generate a random number to check return values for.
    $this->returnValue = (string) mt_rand(0, PHP_INT_MAX);

    // Guzzle closes this stream automatically.
    $this->handle = fopen('php://temp', 'rw');
    fwrite($this->handle, $this->returnValue);
  }

  protected function getMockResponse() {
    $body = EntityBody::factory($this->handle);

    $response = $this->getMockBuilder('\Guzzle\Http\Message\Response')
                     ->disableOriginalConstructor()
                     ->getMock();

    $response->expects($this->exactly(4))
      ->method('getBody')
      ->will($this->returnValue($body));

    return $response;
  }

  public function testGuzzlePayload() {
    $response = $this->getMockResponse();
    $payload = new GuzzlePayload($response);

    $this->assertSame($this->handle, $payload->getStream());
    $this->assertSame(strlen($this->returnValue), $payload->getSize());
    $this->assertSame($this->returnValue, $payload->getContents());
    $this->assertSame('php://temp', $payload->getPath());
  }

}
