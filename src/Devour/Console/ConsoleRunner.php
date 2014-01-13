<?php

/**
 * @file
 * Contains \Devour\Console\ConsoleRunner.
 */

namespace Devour\Console;

use Devour\Console\Command\BuildCommand;
use Devour\Console\Command\ClearCommand;
use Devour\Console\Command\ImportCommand;
use Devour\Devour;
use Devour\Importer\ImporterFactory;
use Devour\Importer\ImporterInterface;
use Devour\Util\FileSystem;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * The Devour console application.
 */
class ConsoleRunner extends Application {

    private static $logo = <<<EOF
  _____
 |  __ \
 | |  | | _____   _____  _   _ _ __
 | |  | |/ _ \ \ / / _ \| | | | '__|
 | |__| |  __/\ V | (_) | |_| | |
 |_____/ \___| \_/ \___/ \__,_|_|


EOF;

  /**
   * The currently running app.
   *
   * @var \Devour\Console\ConsoleRunner|false
   */
  private static $runningApp = FALSE;

  /**
   * The current importer.
   *
   * @var \Devour\Importer\ImporterInterface
   */
  protected $importer;

  /**
   * The current importer configuration file.
   *
   * @var string
   */
  protected $configFile;

  /**
   * {@inheritdoc}
   */
  public function __construct() {
    if (function_exists('ini_set')) {
      ini_set('xdebug.show_exception_trace', FALSE);
      ini_set('xdebug.scream', FALSE);
    }
    if (function_exists('date_default_timezone_set') && function_exists('date_default_timezone_get')) {
      date_default_timezone_set(@date_default_timezone_get());
    }

    Devour::registerDefaults();

    parent::__construct('Devour', 'DEV');
  }

  /**
   * {@inheritdoc}
   */
  public function run(InputInterface $input = NULL, OutputInterface $output = NULL) {
    static::$runningApp = $this;
    return parent::run($input, $output);
  }

  /**
   * Runs the console application.
   */
  public static function runApplication(InputInterface $input = NULL, OutputInterface $output = NULL, $auto_exit = TRUE) {
    static::$runningApp = new static();
    static::$runningApp->setAutoExit($auto_exit);
    static::$runningApp->run($input, $output);
  }

  /**
   * Returns the currently running application.
   *
   * This can be called from a bootstrap file to add commands.
   *
   * @return \Devour\Console\ConsoleRunner|false
   */
  public static function getApplication() {
    return static::$runningApp;
  }

  public function getImporter() {
    if (!$this->importer && $this->configFile) {
      $this->importer = ImporterFactory::fromConfigurationFile($this->configFile);
    }

    return $this->importer;
  }

  public function setImporter(ImporterInterface $importer) {
    $this->importer = $importer;
    return $this;
  }

  public function getImporterConfigurationFile() {
    return $this->configFile;
  }

  /**
   * {@inheritdoc}
   */
  public function getHelp() {
    return static::$logo . parent::getHelp();
  }

  /**
   * {@inheritDoc}
   */
  public function doRun(InputInterface $input, OutputInterface $output) {
    if ($input->hasParameterOption('--profile')) {
      $startTime = microtime(TRUE);
    }

    $this->configFile = $this->getImporterConfigFile($input);

    if ($bootstrap = $this->getBootstrapFile($input)) {
      require_once $bootstrap;
    }

    $result = parent::doRun($input, $output);

    // Jacked from Composer.
    if (isset($startTime)) {
      $output->writeln('<info>Memory usage: ' . round(memory_get_usage() / 1024 / 1024, 2) . 'MB (peak: ' . round(memory_get_peak_usage() / 1024 / 1024, 2) . 'MB), time: ' . round(microtime(true) - $startTime, 2) . 's');
    }

    return $result;
  }

  protected function getImporterConfigFile(InputInterface $input) {
    $config = $input->getParameterOption(array('--config', '-c'));

    if ($config !== FALSE) {
      if (FileSystem::checkFile($config)) {
        return $config;
      }
      else {
        throw new \RuntimeException(sprintf('Unable to read "%s".', $config));
      }
    }

    if (FileSystem::checkFile('devour.yml')) {
      return 'devour.yml';
    }
  }

  protected function getBootstrapFile(InputInterface $input) {
    $bootstrap = $input->getParameterOption(array('--bootstrap', '-b'));

    if ($bootstrap !== FALSE && !FileSystem::checkFile($bootstrap)) {
      throw new \RuntimeException('Invalid bootstrap file.');
    }

    return $bootstrap;
  }

  /**
   * {@inheritdoc}
   */
  protected function getDefaultCommands() {
    $commands = parent::getDefaultCommands();
    $commands[] = new ImportCommand();
    $commands[] = new ClearCommand();
    // $commands[] = new BuildCommand();

    return $commands;
  }

  /**
   * {@inheritdoc}
   */
  protected function getDefaultInputDefinition() {
    $definition = parent::getDefaultInputDefinition();
    $definition->addOption(new InputOption('config', 'c', InputOption::VALUE_REQUIRED, 'The configuration file.'));
    $definition->addOption(new InputOption('profile', NULL, InputOption::VALUE_NONE, 'Display timing and memory usage information'));
    $definition->addOption(new InputOption('bootstrap', 'b', InputOption::VALUE_REQUIRED, 'The file used to include your application.'));

    return $definition;
  }

}
