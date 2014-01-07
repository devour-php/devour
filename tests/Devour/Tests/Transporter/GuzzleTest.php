<?php

namespace Devour\Tests\Transporter;

use Devour\Source\SourceInterface;
use Devour\Transporter\File;
use Devour\Transporter\Guzzle;
use Guzzle\Http\Message\Response;
use Guzzle\Plugin\Mock\MockPlugin;
use Guzzle\Tests\GuzzleTestCase;

class GuzzleTest extends GuzzleTestCase {

  protected $transporter;

  protected $mockPlugin;

  public function setUp() {
    $this->mockPlugin = new MockPlugin();
    $this->transporter = new Guzzle();
    $this->transporter->addSubscriber($this->mockPlugin);
  }

  protected function getMockSource($filepath) {
    $source = $this->getMock('\Devour\Source\SourceInterface');

    $source->expects($this->any())
      ->method('getSource')
      ->will($this->returnValue($filepath));

    return $source;
  }

  public function testGuzzle() {
    $this->mockPlugin->addResponse(new Response(200, NULL, 'Good boy.'));

    $source = $this->getMockSource('http://example.com');
    $payload = $this->transporter->transport($source);
    $this->assertSame('Good boy.', $payload->getContents());
  }

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

    $source = $this->getMockSource('http://example.com');
    $this->transporter->transport($source);
  }

  /**
   * @expectedException \RuntimeException
   * @expectedExceptionMessage [curl] 3: <url> malformed [url] /badurl
   */
  public function testBadUrl() {
    $transporter = new Guzzle();
    $source = $this->getMockSource('badurl');
    $transporter->transport($source);
  }

}
