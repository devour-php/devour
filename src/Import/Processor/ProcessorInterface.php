<?php

/**
 * @file
 * Contains \Import\Processor\ProcessorInterface.
 */

namespace Import\Processor;

use Import\Payload\ParsedPayloadInterface;

/**
 * The interface that all processors must implement.
 *
 * @todo
 */
interface ProcessorInterface {

  /**
   * Processes a parsed payload.
   *
   * @param \Import\Payload\ParserInterface $payload
   */
  public function process(ParsedPayloadInterface $payload);

}
