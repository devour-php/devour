<?php

/**
 * @file
 * Contains \Devour\Parser\ParserInterface.
 */

namespace Devour\Parser;

use Devour\Payload\RawPayloadInterface;

/**
 * The interface all parsers must implement.
 */
interface ParserInterface {

  /**
   * Parses a raw payload.
   *
   * @param \Devour\Payload\RawPayloadInterface $payload
   *   The raw payload.
   *
   * @return \Devour\Payload\ParsedPayloadInterface
   *   A parsed payload.
   */
  public function parse(RawPayloadInterface $payload);

}
