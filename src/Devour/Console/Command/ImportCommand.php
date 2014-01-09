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
use Symfony\Component\Console\Input\InputArgument;
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
      ->addArgument('source', InputArgument::REQUIRED | InputArgument::IS_ARRAY, 'The source of the import.')
      ->addOption('config', 'c', InputOption::VALUE_OPTIONAL, 'The source of the import.', 'devour.yml')
      ->addOption('concurrency', NULL, InputOption::VALUE_OPTIONAL, 'The number of parallel proceses to execute.', 1);
  }

  /**
   * {@inheritdoc}
   */
  protected function execute(InputInterface $input, OutputInterface $output) {
    $config = $input->getOption('config');
    $sources = $input->getArgument('source');
    $concurrency = $input->getOption('concurrency');

    $importer = ImporterFactory::fromConfigurationFile($config);
    $this->executeParallel($importer, $sources, $concurrency, $config);
  }

  /**
   * Executes an import in parallel.
   */
  protected function executeParallel(ImporterInterface $importer, array $sources, $num_processes, $config) {
    $process_group = new \SplObjectStorage();

    foreach ($sources as $source) {
      $this->doExecute($process_group, $importer, new Source($source), $num_processes, $config);
    }

    // Ensure all processes have finished.
    foreach ($process_group as $process) {
      $process->wait();
      echo $process->getOutput();
    }
  }

  protected function doExecute(\SplObjectStorage $process_group, ImporterInterface $importer, SourceInterface $source, $num_processes, $config) {
    $script_path = DEVOUR_COMMAND_START_DIR . '/batch.php';

    do {
      $this->limitProcess($process_group, $num_processes);

      $stream = $importer->transport($source);

      $args = array('php', $script_path, $config, (string) $source, $stream->getUri());

      $builder = new ProcessBuilder($args);
      $process = $builder->getProcess();

      $process->start();
      $process_group->attach($process);

    } while ($importer->transporter instanceof ProgressInterface && $importer->transporter->progress($source) != ProgressInterface::COMPLETE);
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
