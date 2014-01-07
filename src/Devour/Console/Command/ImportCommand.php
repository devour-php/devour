<?php

/**
 * @file
 * Contains \Devour\Console\Command\ImportCommand.
 */

namespace Devour\Console\Command;

use Devour\Importer\ImporterFactory;
use Devour\Source\Source;
use Devour\Util\FileSystem;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Command to execute an import.
 */
class ImportCommand extends Command {

  /**
   * {@inheritdoc}
   */
  protected function configure() {
    $this
      ->setName('import')
      ->setDescription('Execute an import.')
      ->addOption('config', 'c', InputOption::VALUE_REQUIRED, 'The path to the configuration file.')
      ->addOption('source', 's', InputOption::VALUE_REQUIRED, 'The source of the import.');
  }

  /**
   * {@inheritdoc}
   */
  protected function execute(InputInterface $input, OutputInterface $output) {
    $config = $input->getOption('config');

    if (!FileSystem::checkFile($config)) {
      $output->writeln('<error>The configuration file does not exist or is not readable.</error>');
      return;
    }

    $importer = ImporterFactory::fromConfigurationFile($config);
    $importer->import(new Source($input->getOption('source')));
  }

}
