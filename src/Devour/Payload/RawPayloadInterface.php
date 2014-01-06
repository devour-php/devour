<?php

/**
 * @file
 * Contains \Devour\Payload\RawPayloadInterface.
 */

namespace Devour\Payload;

/**
 * A raw payload is a wrapper around a resource that gets passed to a parser.
 */
interface RawPayloadInterface {

  /**
   * Returns the path to the payload.
   *
   * This can be a file path, or an in-memory file.
   *
   * @return string
   *   The path of the payload.
   */
  public function getPath();

  /**
   * Returns the contents of the raw payload.
   *
   * @return string
   *   The contents of the payload.
   */
  public function getContents();

}
