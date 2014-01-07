<?php

/**
 * @file
 * Contains \Devour\Payload\GuzzlePayload.
 */

namespace Devour\Payload;

use Guzzle\Http\Message\Response;

/**
 * @todo Add stream handling.
 */
class GuzzlePayload implements PayloadInterface {

  /**
   * The Guzzle response.
   *
   * @var \Guzzle\Http\Message\Response
   */
  protected $response;

  /**
   * Constructs a new GuzzlePayload object.
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
