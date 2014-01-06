<?php

/**
 * @file
 * Contains \Devour\Transport\TransportInterface.
 */

namespace Devour\Transport;

use Devour\Source\SourceInterface;

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
   * @param \Devour\Source\SourceInterface $source
   *   A source object.
   *
   * @return \Devour\Payload\RawPayloadInterface
   *   A raw payload object.
   *
   * @throws \RuntimeException
   *   Thrown if an error occured.
   */
  public function getRawPayload(SourceInterface $source);

}
