<?php

/**
 * @file
 * Contains \Devour\Console\Command\ClearCommand.
 */

namespace Devour\Console\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Command to clear a source.
 */
class ClearCommand extends DevourCommand {

  /**
   * {@inheritdoc}
   */
  protected function configure() {
    $this->setName('clear')
         ->setImporterRequired()
         ->addSourceArgument()
         ->setDescription('Clear one or more sources.');
  }

  /**
   * {@inheritdoc}
   */
  protected function execute(InputInterface $input, OutputInterface $output) {
    $importer = $this->getImporter();

    foreach ($this->getSources($input) as $source) {
      $importer->clear($source);
    }
  }

}
