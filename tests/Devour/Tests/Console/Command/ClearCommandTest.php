<?php

/**
 * @file
 * Contains \Devour\Tests\Console\Command\ClearCommandTest.
 */

namespace Devour\Tests\Console\Command;

use Devour\Console\Command\ClearCommand;
use Devour\Tests\DevourTestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Yaml\Dumper;

/**
 * @covers \Devour\Console\Command\ClearCommand
 */
class ClearCommandTest extends DevourTestCase {

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
    $application->add(new ClearCommand());

    $command = $application->find('clear');
    $commandTester = new CommandTester($command);
    $commandTester->execute(array('command' => $command->getName(), 'source' => array(''), '--config' => static::FILE_PATH));
  }

}
