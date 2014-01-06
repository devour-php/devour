<?php

/**
 * @file
 * Contains \Devour\Console\ConsoleRunner.
 */

namespace Devour\Console;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Helper\HelperSet;

/**
 * Wrapper for running the console.
 */
class ConsoleRunner {

  /**
   * Executes the console.
   */
  public static function run() {
    $cli = new Application('Import Command Line Interface');
    // $cli->setCatchExceptions(true);
    // $cli->setHelperSet($helperSet);
    self::addCommands($cli);
    $cli->run();
  }

  /**
   * Adds commands to the application.
   *
   * @param \Symfony\Component\Console\Application $cli
   *   The cli application.
   */
  public static function addCommands(Application $cli) {
    $cli->addCommands(array(
      new \Devour\Console\Command\ImportCommand(),
    ));
  }

}
