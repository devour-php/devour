<?php

/**
 * @file
 * Contains \Devour\Tests\Console\Command\DevourCommandTest.
 */

namespace Devour\Tests\Console\Command;

use Devour\Console\ConsoleRunner;
use Devour\Importer\Importer;
use Devour\Tests\DevourTestCase;

/**
 * @covers \Devour\Console\Command\DevourCommand
 */
class DevourCommandTest extends DevourTestCase {

  protected $app;

  protected $command;

  const FILE = 'sources_file.txt';

  public function setUp() {
    $this->app = new ConsoleRunner();
    $this->command = $this->getMockForAbstractClass('Devour\Console\Command\DevourCommand', array('test_command'));
    $this->command->setApplication($this->app);
  }


  public function testGetImporter() {
    $importer = new Importer();

    $method = $this->getMethod('Devour\Console\Command\DevourCommand', 'getImporter');
    $this->assertNull($method->invoke($this->command));

    $this->app->setImporter($importer);
    $this->assertSame($importer, $method->invoke($this->command));
  }

  /**
   * @expectedException \RuntimeException
   * @expectedExceptionMessage Unable to find the importer. Please specify a configuration file.
   */
  public function testGetImporterException() {
    $method = $this->getMethod('Devour\Console\Command\DevourCommand', 'getImporter');
    $this->assertNull($method->invoke($this->command));

    $importer_required = $this->getMethod('Devour\Console\Command\DevourCommand', 'setImporterRequired');
    $importer_required->invoke($this->command);

    $method->invoke($this->command);
  }

  public function testGetSources() {
    $method = $this->getMethod('Devour\Console\Command\DevourCommand', 'getSources');

    $input = $this->getMock('Symfony\Component\Console\Input\InputInterface');
    $input->expects($this->once())
          ->method('getArgument')
          ->will($this->returnValue(array('http://example.com')));
    $return = $method->invoke($this->command, $input);
    $this->assertSame('http://example.com', (string) array_pop($return));

    $sources = array('http://example.com', 'http://example.net');
    file_put_contents(static::FILE, implode("\n", $sources));

    $input = $this->getMock('Symfony\Component\Console\Input\InputInterface');
    $input->expects($this->once())
          ->method('getArgument')
          ->will($this->returnValue(array(static::FILE)));
    $input->expects($this->once())
          ->method('getOption')
          ->will($this->returnValue(TRUE));

    $return = $method->invoke($this->command, $input);
    $this->assertSame(2, count($return));
  }

}
