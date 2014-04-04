<?php

/**
 * @file
 * Contains \Devour\Console\Command\DevourCommand.
 */

namespace Devour\Console\Command;

use Devour\Source\Source;
use Devour\Util\FileSystem;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;

/**
 * Helper class for Devour commands.
 */
abstract class DevourCommand extends Command {

  /**
   * Whether this importer requires an importer.
   *
   * @var bool
   */
  protected $importerRequired = FALSE;

  /**
   * Returns the current importer.
   *
   * This command should not try to catch the exception.
   *
   * @return \Devour\Importer\ImporterInterface
   *
   * @throws \RuntimeException
   *   Thrown if the command requires an importer, but one is not available.
   */
  protected function getImporter() {
    if ($importer = $this->getApplication()->getImporter()) {
      return $importer;
    }
    if ($this->importerRequired) {
      throw new \RuntimeException('Unable to find the importer. Please specify a configuration file.');
    }
  }

  /**
   * Declares that this command requires an importer.
   *
   * @return self
   *   The command for chaining.
   */
  protected function setImporterRequired() {
    $this->importerRequired = TRUE;
    return $this;
  }

  protected function addSourceArgument() {
    $this->addArgument('source', InputArgument::REQUIRED | InputArgument::IS_ARRAY, 'The source of the import.')
         ->addOption('source_file', NULL, InputOption::VALUE_NONE, 'Specifies that the source is a file that contains a list of sources, one per line.');

    return $this;
  }

  /**
   * Gets the sources provided in the input.
   *
   * @return \Devour\Source\SourceInterface[]
   *   A list of source objects.
   */
  protected function getSources(InputInterface $input) {
    $sources = $input->getArgument('source');

    // Sources could be a file that contains a list of sources.
    if ($input->getOption('source_file') && FileSystem::checkFile($sources[0])) {
      $sources = file_get_contents($sources[0]);
      $sources = array_map('trim', explode("\n", $sources));
    }

    return array_map(function($source) {
      return new Source($source);
    }, array_filter($sources));
  }

}
