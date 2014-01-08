<?php

/**
 * @file
 * Contains \Devour\Console\Command\ClearCommand.
 */

namespace Devour\Console\Command;

use Devour\Importer\ImporterFactory;
use Devour\Importer\ImporterInterface;
use Devour\ProgressInterface;
use Devour\Source\Source;
use Devour\Source\SourceInterface;
use Devour\Util\FileSystem;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\ProcessBuilder;

/**
 * Command to clear a source.
 */
class ClearCommand extends Command {

  /**
   * {@inheritdoc}
   */
  protected function configure() {
    $this
      ->setName('clear')
      ->setDescription('Clear one or more sources.')
      ->addArgument('source', InputArgument::REQUIRED | InputArgument::IS_ARRAY, 'The source of the import.')
      ->addOption('config', 'c', InputOption::VALUE_OPTIONAL, 'The source of the import.', 'devour.yml');
  }

  /**
   * {@inheritdoc}
   */
  protected function execute(InputInterface $input, OutputInterface $output) {
    $config = $input->getOption('config');
    $sources = $input->getArgument('source');

    $importer = ImporterFactory::fromConfigurationFile($config);
    $this->executeClear($importer, $sources);
  }

  /**
   * Clears a source.
   */
  protected function executeClear(ImporterInterface $importer, array $sources) {
    foreach ($sources as $source) {
      $importer->clear(new Source($source));
    }
  }

}
