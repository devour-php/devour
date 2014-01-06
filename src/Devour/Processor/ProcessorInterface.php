<?php

/**
 * @file
 * Contains \Devour\Processor\ProcessorInterface.
 */

namespace Devour\Processor;

use Devour\Payload\ParsedPayloadInterface;

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
   */
  public function process(ParsedPayloadInterface $payload);

}
