<?php

/**
 * @file
 * Contains \Devour\Tests\Transporter\GuzzleTest.
 */

namespace Devour\Tests\Transporter;

use Devour\Source\Source;
use Devour\Transporter\Guzzle;
use Guzzle\Http\Message\Response;
use Guzzle\Plugin\Mock\MockPlugin;
use Guzzle\Tests\GuzzleTestCase;

/**
 * @covers \Devour\Transporter\Guzzle
 */
class GuzzleTest extends GuzzleTestCase {

  protected $transporter;

  protected $mockPlugin;

  public function setUp() {
    $this->mockPlugin = new MockPlugin();
    $this->transporter = new Guzzle();
    $this->transporter->addSubscriber($this->mockPlugin);
  }

  public function testGuzzle() {
    $this->mockPlugin->addResponse(new Response(200, NULL, 'Good boy.'));

    $payload = $this->transporter->transport(new Source('http://example.com'));
    $this->assertSame('Good boy.', $payload->getContents());
  }

  /**
   * @covers \Devour\Transporter\Guzzle::fromConfiguration
   */
  public function testFromConfiguration() {
    $configuration = array(
      'request.options' => array(
        'headers' => array('X-Foo' => 'Bar'),
      ),
    );

    $transporter = Guzzle::fromConfiguration($configuration);
    $config = $transporter->getConfig('request.options');
    $this->assertSame($configuration['request.options'], $config);
  }

  /**
   * @expectedException \RuntimeException
   */
  public function testGuzzle404() {
    $this->mockPlugin->addResponse(new Response(404));
    $this->transporter->transport(new Source('http://example.com'));
  }

  /**
   * @expectedException \RuntimeException
   * @expectedExceptionMessage [curl] 3: <url> malformed [url] /badurl
   */
  public function testBadUrl() {
    $transporter = new Guzzle();
    $transporter->transport(new Source('badurl'));
  }

}
