<?php

/**
 * @file
 * Contains \Devour\Tests\Console\Command\ImportCommandTest.
 */

namespace Devour\Tests\Console\Command;

use Devour\Console\Command\ImportCommand;
use Devour\Tests\DevourTestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Process\Process;
use Symfony\Component\Yaml\Dumper;

/**
 * @covers \Devour\Console\Command\ImportCommand
 */
class ImportCommandTest extends DevourTestCase {

  const FILE_PATH = './tpm_config';

  public function setUp() {
    $this->configuration = array(
      'importer' => array(
        'class' => '\Devour\Importer\Importer',
      ),
      'transporter' => array(
        'class' => '\Devour\Tests\Transporter\TransporterStub',
      ),
      'parser' => array(
        'class' => '\Devour\Tests\Parser\ParserStub',
      ),
      'processor' => array(
        'class' => '\Devour\Tests\Processor\StubProcessor',
      ),
    );

    $dumper = new Dumper();
    file_put_contents(static::FILE_PATH, $dumper->dump($this->configuration));
  }

  public function tearDown() {
    unlink(static::FILE_PATH);
  }

  public function testCommand() {
    $application = new Application();
    $application->add(new ImportCommand());

    $command = $application->find('import');
    $commandTester = new CommandTester($command);
    $commandTester->execute(array('command' => $command->getName(), '--config' => static::FILE_PATH, '--source' => '', '--concurrency' => 1));

    // $this->assertRegExp('/.../', $commandTester->getDisplay());
  }

  public function testCommandNoConfig() {
    $application = new Application();
    $application->add(new ImportCommand());

    $command = $application->find('import');
    $commandTester = new CommandTester($command);
    $commandTester->execute(array('command' => $command->getName(), '--source' => ''));

    $this->assertSame('The configuration file does not exist or is not readable.', trim($commandTester->getDisplay()));
  }

  public function testLimitProcess() {
    $method = $this->getMethod('Devour\Console\Command\ImportCommand', 'limitProcess');
    $command = new ImportCommand();
    $process_group = new \SplObjectStorage();

    foreach (range(1, 4) as $i) {
      $process = $this->getMockBuilder('Symfony\Component\Process\Process')
                      ->disableOriginalConstructor()
                      ->getMock();
      // We can't depend on the order in SplObjectStorage when iterating, so
      // these might get not all get called if the baddy gets called first.
      $process->expects($this->any())
              ->method('isRunning')
              ->will($this->returnValue(TRUE));
      $process_group->attach($process);
    }

    // The baddy.
    $process = $this->getMockBuilder('Symfony\Component\Process\Process')
                    ->disableOriginalConstructor()
                    ->getMock();
    $process->expects($this->once())
            ->method('isRunning')
            ->will($this->returnValue(FALSE));
    $process_group->attach($process);

    $method->invokeArgs($command, array($process_group, 5));
    $this->assertSame(4, count($process_group));
  }

}
