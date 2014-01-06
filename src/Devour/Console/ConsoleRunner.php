<?php

namespace Devour\Console;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Helper\HelperSet;

class ConsoleRunner {

  public static function run() {
    $cli = new Application('Import Command Line Interface');
    // $cli->setCatchExceptions(true);
    // $cli->setHelperSet($helperSet);
    self::addCommands($cli);
    $cli->run();
  }

  public static function addCommands(Application $cli) {
    $cli->addCommands(array(
      new \Devour\Console\Command\ImportCommand(),
    ));
  }

}
