<?php

/**
 * @file
 * Contains \Devour\Transport\Guzzle.
 */

namespace Devour\Transport;

use Devour\Source\SourceInterface;

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
