<?php

/**
 * @file
 * Contains \Devour\Transporter\Guzzle.
 */

namespace Devour\Transporter;

use Devour\Payload\GuzzlePayload;
use Devour\Source\SourceInterface;
use Guzzle\Http\Client;

/**
 * A transport that fetches a payload via HTTP.
 */
class Guzzle extends Client implements TransporterInterface {

  /**
   * {@inheritdoc}
   */
  public function transport(SourceInterface $source) {
    $request = $this->get($source->getSource());
    $response = $request->send();
    return new GuzzlePayload($response);
  }

  /**
   * {@inheritdoc}
   */
  public static function fromConfiguration(array $configuration) {
    return new static(NULL, $configuration);
  }

}
