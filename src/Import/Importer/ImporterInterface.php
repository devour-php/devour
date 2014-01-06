<?php

/**
 * @file
 * Contains \Import\Importer\ImporterInterface.
 */

namespace Import\Importer;

use Import\Parser\ParserInterface;
use Import\Processor\ProcessorInterface;
use Import\Source\SourceInterface;
use Import\Transport\TransportInterface;

interface ImporterInterface {

  public function import(SourceInterface $source);

}
