<?php

/**
 * @file
 * Contains \Devour\Importer\ImporterInterface.
 */

namespace Devour\Importer;

use Devour\Parser\ParserInterface;
use Devour\Processor\ProcessorInterface;
use Devour\Source\SourceInterface;
use Devour\Transport\TransportInterface;

interface ImporterInterface {

  /**
   * Performs an import.
   *
   * @param \Devour\Source\SourceInterface $source
   *   The source to import from.
   */
  public function import(SourceInterface $source);

}
