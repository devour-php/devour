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
 * A transport is a method of retrieving a paylod. Transporters should strive to
 * be payload agnostic.
 */
interface TransporterInterface {

  /**
   * Returns the raw payload.
   *
   * @param \Devour\Source\SourceInterface $source
   *   A source object.
   *
   * @return \Devour\Payload\PayloadInterface
   *   A raw payload object.
   *
   * @throws \RuntimeException
   *   Thrown if an error occured.
   */
  public function transport(SourceInterface $source);

}