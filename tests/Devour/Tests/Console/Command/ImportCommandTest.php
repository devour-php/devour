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
use Symfony\Component\Yaml\Dumper;

/**
 * @covers \Devour\Console\Command\ImportCommand
 */
class ImportCommandTest extends DevourTestCase {

  const FILE_PATH = 'tpm_config';

  const FILE_SOURCE = 'source_file';

  const DIRECTORY = 'tmp_dir';

  const FILE_IN_DIR = 'tmp_dir/file';

  public function setUp() {
    $this->configuration = array(
      'importer' => array(
        'class' => 'Devour\Importer\Importer',
      ),
      'transporter' => array(
        'class' => 'Devour\Tests\Transporter\TransporterStub',
      ),
      'parser' => array(
        'class' => 'Devour\Tests\Parser\ParserStub',
      ),
      'processor' => array(
        'class' => 'Devour\Tests\Processor\ProcessorStub',
      ),
    );

    $dumper = new Dumper();
    file_put_contents(static::FILE_PATH, $dumper->dump($this->configuration));
    touch(static::FILE_SOURCE);
  }

  public function testCommand() {
    $application = new Application();
    $application->add(new ImportCommand());

    $command = $application->find('import');
    $commandTester = new CommandTester($command);
    $commandTester->execute(array('command' => $command->getName(), 'source' => array(''), '--config' => static::FILE_PATH, '--concurrency' => 1));
  }

  public function testCommandSourceFile() {
    $application = new Application();
    $application->add(new ImportCommand());
    touch(static::FILE_SOURCE);

    $command = $application->find('import');
    $commandTester = new CommandTester($command);
    $commandTester->execute(array('command' => $command->getName(), 'source' => array(static::FILE_SOURCE), '--config' => static::FILE_PATH, '--source_file' => TRUE));
  }

  public function testCommandSameProcess() {
    mkdir(static::DIRECTORY);
    touch(static::FILE_IN_DIR);
    $this->configuration['transporter']['class'] = 'Devour\Transporter\Directory';

    $dumper = new Dumper();
    file_put_contents(static::FILE_PATH, $dumper->dump($this->configuration));

    $application = new Application();
    $application->add(new ImportCommand());

    $command = $application->find('import');
    $commandTester = new CommandTester($command);
    $commandTester->execute(array('command' => $command->getName(), 'source' => array(static::DIRECTORY), '--config' => static::FILE_PATH));
  }

  public function testCommandNewProcess() {
    $this->configuration['transporter']['class'] = 'Devour\Transporter\File';

    $dumper = new Dumper();
    file_put_contents(static::FILE_PATH, $dumper->dump($this->configuration));
    touch(static::FILE_SOURCE);

    $application = new Application();
    $application->add(new ImportCommand());

    $command = $application->find('import');
    $commandTester = new CommandTester($command);
    $commandTester->execute(array('command' => $command->getName(), 'source' => array(static::DIRECTORY), '--config' => static::FILE_PATH));
  }

  /**
   * @expectedException \RuntimeException
   * @expectedExceptionMessage The configuration file "devour.yml" does not exist or is not readable.
   */
  public function testCommandNoConfig() {
    $application = new Application();
    $application->add(new ImportCommand());

    $command = $application->find('import');
    $commandTester = new CommandTester($command);
    $commandTester->execute(array('command' => $command->getName(), 'source' => ''));
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

    $method->invokeArgs($command, array($process_group, 5));
    $this->assertSame(4, count($process_group));
  }

  /**
   * @covers \Devour\Console\Command\ImportCommand::printProcess
   */
  public function testprintProcess() {
    $method = $this->getMethod('Devour\Console\Command\ImportCommand', 'printProcess');
    $command = new ImportCommand();

    $process = $this->getMockProcess(FALSE, $this->never());
    $process->expects($this->once())
            ->method('getErrorOutput')
            ->will($this->returnValue(TRUE));

    $method->invokeArgs($command, array($process));

    $property = $this->getProperty('Devour\Console\Command\ImportCommand', 'errors');
    $this->assertSame(1, count($property->getValue($command)));
  }

/**
   * @covers \Devour\Console\Command\ImportCommand::executeParallel
   * @expectedException \RuntimeException
   * @expectedExceptionMessage hi
   */
  public function testexecuteParallelException() {
    $property = $this->getProperty('Devour\Console\Command\ImportCommand', 'errors');
    $command = new ImportCommand();

    $property->setValue($command, array(array('message' => 'hi', 'code' => 1234)));
    $method = $this->getMethod('Devour\Console\Command\ImportCommand', 'executeParallel');

    $output = $this->getMock('Symfony\Component\Console\Output\OutputInterface');
    $importer = $this->getMock('Devour\Importer\ImporterInterface');
    $method->invokeArgs($command, array($output, $importer, array(), 1, 'beep'));
  }

}
