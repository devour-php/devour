<?php

/**
 * @file
 * Contains \Devour\Payload\FilePayload.
 */

namespace Devour\Payload;

/**
 * @todo After rename, and stream handling.
 */
class FilePayload implements PayloadInterface {

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
