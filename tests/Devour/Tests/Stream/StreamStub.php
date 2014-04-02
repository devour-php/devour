<?php

/**
 * @file
 * Contains \Devour\Tests\Stream\StreamStub.
 */

namespace Devour\Tests\Stream;

use GuzzleHttp\Stream\Stream;

/**
 * A stub stream implementation.
 */
class StreamStub extends Stream {

  /**
   * Constructs a StreamStub object.
   */
  public function __construct($file = NULL, $raw = FALSE) {
    if ($file && $raw) {
      $handle = fopen('php://temp', 'w+');
      fwrite($handle, $file);

      return parent::__construct($handle, strlen($file));
    }

    if (!$file) {
      $file = 'php://temp';
    }

    parent::__construct(fopen($file, 'r+'), NULL);
  }

}
