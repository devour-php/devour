<?php

/**
 * @file
 * Contains \Import\Transport\TransportInterface.
 */

namespace Import\Transport;

use Import\Source\SourceInterface;

/**
 * The interface all transports must implement.
 *
 * A transport is a method of retrieving a paylod. Transports should strive to
 * be payload agnostic.
 */
interface TransportInterface {

  /**
   * Returns the raw payload.
   *
   * @param \Import\Source\SourceInterface $source
   *   A source object.
   *
   * @return \Import\Payload\RawPayloadInterface
   *   A raw payload object.
   *
   * @throws \RuntimeException
   *   Thrown if an error occured.
   */
  public function getRawPayload(SourceInterface $source);

}
