<?php

/**
 * @file
 * Contains \Devour\Tests\Console\ConsoleRunnerTest.
 */

namespace Devour\Tests\Console;

use Devour\Console\ConsoleRunner;
use Devour\Tests\DevourTestCase;
use Symfony\Component\Console\Application;

/**
 * @covers \Devour\Console\ConsoleRunner
 * @todo More tests.
 */
class ConsoleRunnerTest extends DevourTestCase {

  public function testGetApp() {
    $app = ConsoleRunner::getApplication();
    $this->assertInstanceOf('Symfony\Component\Console\Application', $app);
  }

  public function testAddCommands() {
    $app = new Application();
    ConsoleRunner::addCommands($app);

    $commands = $app->all();
    $this->assertTrue(isset($commands['import']));
  }

}
