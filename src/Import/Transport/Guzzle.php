<?php

/**
 * @file
 * Contains \Import\Transport\Guzzle.
 */

namespace Import\Transport;

use Import\Source\SourceInterface;

/**
 * A transport that fetches a payload via HTTP.
 */
class Guzzle implements TransportInterface {

  /**
   * {@inheritdoc}
   */
  public function getRawPayload(SourceInterface $source) {

  }

}
