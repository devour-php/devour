<?php

/**
 * @file
 * Contains \Devour\Transporter\TransporterInterface.
 */

namespace Devour\Transporter;

use Devour\Source\SourceInterface;

/**
 * The interface all transports must implement.
 *
 * A transport is a method of retrieving a stream. Transporters should strive to
 * be payload agnostic, meaning, they shouldn't care about the contents of the
 * stream.
 */
interface TransporterInterface {

  /**
   * Returns the stream.
   *
   * @param \Devour\Source\SourceInterface $source
   *   A source object.
   *
   * @return \Guzzle\Stream\StreamInterface
   *   A stream object.
   *
   * @throws \RuntimeException
   *   Thrown if an error occured.
   */
  public function transport(SourceInterface $source);

}
