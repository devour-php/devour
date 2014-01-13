<?php

/**
 * @file
 * Contains \Devour\Console\Command\ImportCommand.
 */

namespace Devour\Console\Command;

use Devour\Common\ProgressInterface;
use Devour\Importer\ImporterFactory;
use Devour\Importer\ImporterInterface;
use Devour\Source\SourceInterface;
use Devour\Util\FileSystem;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\ProcessBuilder;

/**
 * Command to execute an import.
 */
class ImportCommand extends DevourCommand {

  /**
   * Any errors collected during execution.
   *
   * @var array.
   */
  protected $errors = array();

  /**
   * {@inheritdoc}
   */
  protected function configure() {
    $this->setName('import')
         ->setImporterRequired()
         ->addSourceArgument()
         ->setDescription('Execute an import.')
         ->addOption('concurrency', NULL, InputOption::VALUE_OPTIONAL, 'The number of parallel proceses to execute.', 1);
  }

  /**
   * {@inheritdoc}
   */
  protected function execute(InputInterface $input, OutputInterface $output) {
    $importer = $this->getImporter();

    $concurrency = $input->getOption('concurrency');

    $sources = $this->getSources($input);

    $config = $this->getApplication()->getImporterConfigurationFile();

    $this->executeParallel($output, $importer, $sources, $concurrency, $config);
  }

  /**
   * Executes an import in parallel.
   */
  protected function executeParallel(OutputInterface $output, ImporterInterface $importer, array $sources, $num_processes, $config) {
    $process_group = new \SplObjectStorage();

    foreach ($sources as $source) {
      $output->writeln(sprintf("<info>Importing: %s</info>", $source));
      $this->doExecute($process_group, $importer, $source, $num_processes, $config);
    }

    // Ensure all processes have finished.
    foreach ($process_group as $process) {
      $this->printProcess($process);
    }

    foreach ($this->errors as $error) {
      throw new \RuntimeException($error['message'], $error['code']);
    }
  }

  protected function doExecute(\SplObjectStorage $process_group, ImporterInterface $importer, SourceInterface $source, $num_processes, $config) {
    $script_path = DEVOUR_COMMAND_START_DIR . '/batch.php';

    do {
      $this->limitProcess($process_group, $num_processes);

      if ($importer->getTransporter()->runInNewProcess()) {
        $args = array('php', $script_path, 'transport', $config, $source);
        $builder = new ProcessBuilder($args);
        $process = $builder->getProcess();

        $process->start();
        $process_group->attach($process);
        return;
      }
      else {
        $stream = $importer->transport($source);
        $args = array('php', $script_path, 'parse', $config, $source, $stream->getUri());
      }

      $builder = new ProcessBuilder($args);
      $process = $builder->getProcess();

      $process->start();
      $process_group->attach($process);

    } while ($importer->getTransporter() instanceof ProgressInterface && $importer->getTransporter()->progress($source) != ProgressInterface::COMPLETE);
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
          $this->printProcess($process);
        }
      }
    }
  }

  protected function printProcess(Process $process) {
    if ($output = $process->getErrorOutput()) {

      // Gather the error messages rather than throwing an exception so that all
      // processes can finish.
      $this->errors[] = array('message' => $output, 'code' => $process->getExitCode());
    }
    else {
      print $process->getOutput();
    }
  }

}
