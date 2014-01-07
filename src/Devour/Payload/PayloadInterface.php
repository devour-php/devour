<?php

/**
 * @file
 * Contains \Devour\Payload\PayloadInterface.
 */

namespace Devour\Payload;

/**
 * A raw payload is a wrapper around a resource that gets passed to a parser.
 */
interface PayloadInterface {

  /**
   * Gets the size of the stream, if available.
   *
   * @return int|bool
   *   The size of the payload, or false if unavailable.
   */
  public function getSize();

  /**
   * Returns a stream containing the payload.
   *
   * @return resource
   *   A streamable resource.
   */
  public function getStream();

  /**
   * Returns the contents of the raw payload.
   *
   * @return string
   *   The contents of the payload.
   */
  public function getContents();

}
