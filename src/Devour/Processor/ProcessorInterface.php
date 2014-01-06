<?php

/**
 * @file
 * Contains \Devour\Processor\ProcessorInterface.
 */

namespace Devour\Processor;

use Devour\Table\TableInterface;

/**
 * The interface that all processors must implement.
 *
 * @todo
 */
interface ProcessorInterface {

  /**
   * Processes a parsed payload.
   *
   * @param \Devour\Payload\ParserInterface $payload
   *   The payload to process.
   */
  public function process(TableInterface $payload);

}
