<?php

/**
 * @file
 * Contains \Devour\Payload\PayloadInterface.
 */

namespace Devour\Payload;

/**
 * A payload is a wrapper around a resource that gets returned from a transport
 * and passed to a parser.
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
   * Returns the contents of the payload.
   *
   * @return string
   *   The contents of the payload.
   */
  public function getContents();

  /**
   * Returns the path of the payload.
   *
   * @return string
   *   The path of the payload.
   */
  public function getPath();

}
