<?php

/**
 * @file
 * Contains \Devour\Tests\Stream\StreamStub.
 */

namespace Devour\Tests\Stream;

use Guzzle\Stream\Stream;

/**
 * A stub stream implementation.
 */
class StreamStub extends Stream {

  /**
   * Constructs a StreamStub object.
   */
  public function __construct($file = NULL) {
    if (!$file) {
      $file = 'php://temp';
    }
    $this->setStream(fopen($file, 'r+'), NULL);
  }

}
