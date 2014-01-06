<?php

/**
 * @file
 * Contains \Devour\Payload\File.
 */

namespace Devour\Payload;

/**
 * @todo After rename, and stream handling.
 */
class File implements RawPayloadInterface {

  protected $filename;

  /**
   * Constructs a new File object.
   *
   * @param string $filename
   *   The name of the file.
   */
  public function __construct($filename) {
    $this->filename = $filename;
  }

  /**
   * Returns the path to the payload.
   */
  public function getPath() {
    return $this->filename;
  }

  /**
   * Returns the contents of the payload.
   */
  public function getContents() {
    return file_get_contents($this->filename);
  }

}
