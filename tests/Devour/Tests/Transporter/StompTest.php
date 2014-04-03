<?php

/**
 * @file
 * Contains \Devour\Tests\Transporter\StompTest.
 */

namespace Devour\Tests\Transporter;

use Devour\Source\Source;
use Devour\Tests\DevourTestCase;
use Devour\Transporter\Stomp;
use FuseSource\Stomp\Frame;
use FuseSource\Stomp\Stomp as StompConnection;

/**
 * @covers \Devour\Transporter\Stomp
 */
class StompTest extends DevourTestCase {

  protected function getMockStompConnection() {


    return $connection;
  }

  public function testParse() {
    $frame = new Frame('CONNECT', ['a' => '1'], 'beep');

    $connection = $this->getMockBuilder('FuseSource\Stomp\Stomp')
                       ->disableOriginalConstructor()
                       ->getMock();
    $connection->expects($this->exactly(2))
               ->method('readFrame')
               ->will($this->onConsecutiveCalls($frame, NULL));

    $stomp = new Stomp($connection);
    $table = $stomp->transport(new Source('/debug/test'));

    $this->assertSame('CONNECT', $table[0]->get('command'));
    $this->assertSame('1', $table[0]->get('a'));
    $this->assertSame('beep', $table[0]->get('body'));

    // Null case.
    $stomp->transport(new Source('/debug/test'));

    // House keeping.
    $this->assertSame($stomp, $stomp->setProcessLimit(1));
    $this->assertSame(0, $stomp->progress(new Source(NULL)));
    $this->assertSame(TRUE, $stomp->runInNewProcess());
  }

  public function testFromConfiguration() {
    $config = ['broker' => 'http://localhost:6163'];
    $stomp = Stomp::fromConfiguration($config);
    $this->assertInstanceOf('Devour\Transporter\Stomp', $stomp);
  }

  /**
   * @expectedException \Devour\Common\Exception\ConfigurationException
   * @expectedExceptionMessage The field "broker" is required.
   */
  public function testFromConfigurationException() {
    $stomp = Stomp::fromConfiguration([]);
  }

}
