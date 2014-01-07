<?php

namespace Devour\Tests\Console;

use Devour\Console\ConsoleRunner;
use Devour\Tests\DevourTestCase;
use Symfony\Component\Console\Application;

/**
 * @todo More tests.
 */
class ConsoleRunnerTest extends DevourTestCase {

  public function testGetApp() {
    $app = ConsoleRunner::getApplication();
    $this->assertSame('Symfony\Component\Console\Application', get_class($app));
  }

  public function testAddCommands() {
    $app = new Application();
    ConsoleRunner::addCommands($app);

    $commands = $app->all();
    $this->assertTrue(isset($commands['import']));
  }

}
