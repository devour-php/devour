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

  public function import(SourceInterface $source);

}
