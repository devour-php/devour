<?php

/**
 * @file
 * Contains \Import\Parser\ParserInterface.
 */

namespace Import\Parser;

use Import\Payload\RawPayloadInterface;

/**
 * The interface all parsers must implement.
 */
interface ParserInterface {

  /**
   * Parses a raw payload.
   *
   * @param \Import\Payload\RawPayloadInterface $payload
   *   The raw payload.
   *
   * @return \Import\Payload\ParsedPayloadInterface
   *   A parsed payload.
   */
  public function parse(RawPayloadInterface $payload);

}
