<?php

/**
 * @file
 * Contains \Devour\Payload\GuzzlePayload.
 */

namespace Devour\Payload;

use Guzzle\Http\Message\Response;

/**
 * A wrapper around a Guzzle response that acts as a payload.
 *
 * Guzzle's SreamInterface is pretty sweet, and we might end up copying more of
 * if to PayloadInterface, which is a shame. But, it's not worth adding a hard
 * dependency on Guzzle.
 */
class GuzzlePayload implements PayloadInterface {

  /**
   * The Guzzle response.
   *
   * @var \Guzzle\Http\Message\Response
   */
  protected $response;

  /**
   * Constructs a GuzzlePayload object.
   *
   * @param \Guzzle\Http\Message\Response $response
   *   A Guzzle response.
   */
  public function __construct(Response $response) {
    $this->response = $response;
  }

  /**
   * {@inheritdoc}
   */
  public function getSize() {
    return $this->response->getBody()->getSize();
  }

  /**
   * {@inheritdoc}
   */
  public function getStream() {
    return $this->response->getBody()->getStream();
  }

  /**
   * {@inheritdoc}
   */
  public function getContents() {
    return (string) $this->response->getBody();
  }

}
