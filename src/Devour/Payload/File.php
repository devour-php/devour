<?php

/**
 * @file
 * Contains \Devour\Payload\File.
 */

namespace Devour\Payload;

class File implements RawPayloadInterface {

  protected $filepath;

  public function __construct($filepath) {
    $this->filepath = $filepath;
  }

  public function getPath() {
    return $this->filepath;
  }

  public function getContents() {
    return file_get_contents($this->filepath);
  }

}
