<?php
/*
 * @file
 * Contains \Import\Console\Command\ImportCommand.
 */

namespace Import\Console\Command;

use Import\Importer\ImporterFactory;
use Import\Source\Source;
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

    if (!is_file($config) || !is_readable($config)) {
      $output->writeln('<error>The configuration file does not exist or is not readable.</error>');
    }

    $importer = ImporterFactory::fromConfigurationFile($config);
    $importer->import(new Source($input->getOption('source')));
  }

}
