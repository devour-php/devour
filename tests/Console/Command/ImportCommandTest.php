<?php

/**
 * @file
 * Contains \Devour\Tests\Console\Command\ImportCommandTest.
 */

namespace Devour\Tests\Console\Command;

use Devour\Common\ProgressInterface;
use Devour\Console\Command\ImportCommand;
use Devour\Console\ConsoleRunner;
use Devour\Importer\Importer;
use Devour\Tests\DevourTestCase;
use Devour\Tests\Stream\StreamStub;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @covers \Devour\Console\Command\ImportCommand
 */
class ImportCommandTest extends DevourTestCase {

  protected $app;

  protected $importer;

  protected $command;

  public function setUp() {
    $this->app = new ConsoleRunner();
    $this->importer = new Importer();

    $transporter = $this->getMock('Devour\Transporter\TransporterInterface');
    $transporter->expects($this->any())
                ->method('transport')
                ->will($this->returnValue(new StreamStub()));
    $this->importer->setTransporter($transporter);

    $this->app->setImporter($this->importer);
    $this->command = new ImportCommand();
    $this->app->add($this->command);
  }

  public function testCommand() {
    $transporter = $this->getMock('Devour\Transporter\TransporterInterface');
    $transporter->expects($this->once())
                ->method('transport')
                ->will($this->returnValue(new StreamStub()));
    $transporter->expects($this->once())
                ->method('progress')
                ->will($this->returnValue(ProgressInterface::COMPLETE));

    $this->importer->setTransporter($transporter);
    $command = $this->app->find('import');
    $commandTester = new CommandTester($command);
    $commandTester->execute(['source' => ['http://example.com']]);
  }

  /**
   * @covers \Devour\Console\Command\ImportCommand::printProcess
   * @depends testCommand
   */
  public function testCommandNewProcess() {
    $transporter = $this->getMock('Devour\Transporter\TransporterInterface');
    $transporter->expects($this->once())
                ->method('runInNewProcess')
                ->will($this->returnValue(TRUE));

    $this->importer->setTransporter($transporter);

    $command = $this->app->find('import');
    $commandTester = new CommandTester($command);
    $commandTester->execute(['source' => ['http://example.com']]);
  }

  /**
   * @covers \Devour\Console\Command\ImportCommand::limitProcess
   */
  public function testLimitProcess() {
    $method = $this->getMethod('Devour\Console\Command\ImportCommand', 'limitProcess');
    $command = new ImportCommand();

    $process_group = new \SplObjectStorage();
    foreach (range(1, 4) as $i) {
      // We can't depend on the order in SplObjectStorage when iterating, so
      // these might get not all get called if the baddy gets called first.
      // Hence, $this->any().
      $process = $this->getMockProcess(TRUE, $this->any());
      $process_group->attach($process);
    }

    // The baddy.
    $process = $this->getMockProcess(FALSE, $this->once());
    $process_group->attach($process);

    $method->invokeArgs($command, [$process_group, 5]);
    $this->assertSame(4, count($process_group));
  }

  /**
   * @covers \Devour\Console\Command\ImportCommand::printProcess
   * @depends testCommand
   */
  public function testprintProcess() {
    $method = $this->getMethod('Devour\Console\Command\ImportCommand', 'printProcess');
    $command = new ImportCommand();

    $process = $this->getMockProcess(FALSE, $this->never());
    $process->expects($this->once())
            ->method('getErrorOutput')
            ->will($this->returnValue(TRUE));

    $method->invokeArgs($command, [$process]);

    $property = $this->getProperty('Devour\Console\Command\ImportCommand', 'errors');
    $this->assertSame(1, count($property->getValue($command)));
  }

/**
   * @covers \Devour\Console\Command\ImportCommand::executeParallel
   * @expectedException \RuntimeException
   * @expectedExceptionMessage hi
   * @depends testCommand
   */
  public function testexecuteParallelException() {
    $property = $this->getProperty('Devour\Console\Command\ImportCommand', 'errors');
    $command = new ImportCommand();

    $property->setValue($command, [['message' => 'hi', 'code' => 1234]]);
    $method = $this->getMethod('Devour\Console\Command\ImportCommand', 'executeParallel');

    $output = $this->getMock('Symfony\Component\Console\Output\OutputInterface');
    $importer = $this->getMock('Devour\Importer\ImporterInterface');
    $method->invokeArgs($command, [$output, $importer, [], 1, 'beep']);
  }

}
