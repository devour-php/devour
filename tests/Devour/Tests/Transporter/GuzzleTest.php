<?php

/**
 * @file
 * Contains \Devour\Tests\Transporter\GuzzleTest.
 */

namespace Devour\Tests\Transporter;

use Devour\Source\Source;
use Devour\Tests\DevourTestCase;
use Devour\Transporter\Guzzle;
use GuzzleHttp\Adapter\MockAdapter;
use GuzzleHttp\Message\Response;
use GuzzleHttp\Plugin\Cache\CachePlugin;
use GuzzleHttp\Stream\Stream;

/**
 * @covers \Devour\Transporter\Guzzle
 */
class GuzzleTest extends DevourTestCase {

  protected $transporter;

  protected $adapter;

  public function setUp() {
    $this->adapter = new MockAdapter();
    $this->transporter = new Guzzle(['adapter' => $this->adapter]);
  }

  public function testGuzzle() {
    $this->adapter->setResponse(new Response(200, [], Stream::factory('Good boy.')));

    $stream = $this->transporter->transport(new Source('http://example.com'));
    $this->assertSame('Good boy.', (string) $stream);

    $this->assertTrue($this->transporter->runInNewProcess());
  }

  /**
   * @covers \Devour\Transporter\Guzzle::fromConfiguration
   */
  public function testFromConfiguration() {
    $configuration['defaults'] = array(
      'allow_redirects' => TRUE,
      'exceptions' => TRUE,
      'headers' => array(
        'X-Foo' => 'Bar',
        'User-Agent' => 'Devour',
      ),
    );

    $transporter = Guzzle::fromConfiguration($configuration);
    $config = $transporter->getDefaultOption();
    unset($config['verify']);
    $this->assertSame($configuration['defaults'], $config);
  }

  /**
   * @expectedException \RuntimeException
   */
  public function testGuzzle404() {
    $this->adapter->setResponse(new Response(404));
    $this->transporter->transport(new Source('http://example.com'));
  }

  /**
   * @expectedException \RuntimeException
   */
  public function testBadUrl() {
    $transporter = new Guzzle();
    $transporter->transport(new Source('badurl'));
  }

  /**
   * @covers \Devour\Transporter\Guzzle::clear
   *
   * @todo
   */
  public function testClear() {
    // $cache = new CachePlugin();
    // $this->transporter->addSubscriber($cache);
    $this->transporter->clear(new Source('http://example.com'));
  }

}
