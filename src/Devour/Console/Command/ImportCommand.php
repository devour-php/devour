<?php

/**
 * @file
 * Contains \Devour\Console\Command\ImportCommand.
 */

namespace Devour\Console\Command;

use Devour\Importer\ImporterFactory;
use Devour\Importer\ImporterInterface;
use Devour\ProgressInterface;
use Devour\Source\Source;
use Devour\Source\SourceInterface;
use Devour\Util\FileSystem;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\ProcessBuilder;

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
      ->addOption('source', 's', InputOption::VALUE_REQUIRED, 'The source of the import.')
      ->addOption('concurrency', NULL, InputOption::VALUE_OPTIONAL, 'The number of parallel proceses to execute.');
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
    $source = new Source($input->getOption('source'));

    $this->executeParallel($importer, $source, $input->getOption('concurrency'), $config);
  }

  /**
   * Executes an import in parallel.
   */
  protected function executeParallel(ImporterInterface $importer, SourceInterface $source, $num_processes, $config) {
    $script_path = START_DIR . '/batch.php';

    $process_group = new \SplObjectStorage();

    do {
      $this->limitProcess($process_group, $num_processes);

      $payload = $importer->transport($source);

      $args = array('php', $script_path, $config, $payload->getPath());

      $builder = new ProcessBuilder($args);
      $process = $builder->getProcess();

      $process->start();
      $process_group->attach($process);

    } while ($importer->transporter instanceof ProgressInterface && $importer->transporter->progress() != ProgressInterface::COMPLETE);

    // Ensure all processes have finished.
    foreach ($process_group as $process) {
      $process->wait();
      echo $process->getOutput();
    }
  }

  /**
   * Limits the number parallel processes.
   */
  protected function limitProcess(\SplObjectStorage $process_group, $num_processes) {
    while (count($process_group) >= $num_processes) {

      usleep(1000);

      foreach ($process_group as $process) {
        if (!$process->isRunning()) {
          $process_group->detach($process);
        }
      }
    }
  }

}
