<?php

/**
 * @file
 * Contains \Devour\Tests\Console\Command\ClearCommandTest.
 */

namespace Devour\Tests\Console\Command;

use Devour\Console\Command\ClearCommand;
use Devour\Console\ConsoleRunner;
use Devour\Importer\Importer;
use Devour\Tests\DevourTestCase;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @covers \Devour\Console\Command\ClearCommand
 */
class ClearCommandTest extends DevourTestCase {

  protected $app;

  protected $importer;

  protected $command;

  public function setUp() {
    $this->app = new ConsoleRunner();
    $this->importer = new Importer();
    $this->app->setImporter($this->importer);
    $this->command = new ClearCommand();
    $this->app->add($this->command);
  }


  public function testCommand() {
    $command = $this->app->find('clear');
    $commandTester = new CommandTester($command);
    $commandTester->execute(['command' => $command->getName(), 'source' => ['http://example.com']]);
  }

}
