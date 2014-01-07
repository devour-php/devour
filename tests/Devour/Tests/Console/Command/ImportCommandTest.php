<?php

namespace Devour\Tests\Console\Command;

use Devour\Console\Command\ImportCommand;
use Devour\Tests\DevourTestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Yaml\Dumper;

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
    $commandTester->execute(array('command' => $command->getName(), '--config' => static::FILE_PATH, '--source' => ''));

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

}
