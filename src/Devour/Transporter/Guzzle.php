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
class Guzzle implements TransporterInterface {

  /**
   * The timeout in seconds.
   *
   * @var int
   */
  protected $timeout = 30;

  /**
   * The maximum number of redirects.
   *
   * @var int
   */
  protected $maxRedirects = 3;

  /**
   * The guzzle client.
   *
   * @var \Guzzle\Http\Client
   */
  protected $client;

  /**
   * {@inheritdoc}
   */
  public function transport(SourceInterface $source) {
    $response = $this->get($source->getSource());
    return new GuzzlePayload($response);
  }

  /**
   * Performs a GET request.
   *
   * @param string $url
   *   The URL to GET.
   *
   * @return \Guzzle\Http\Message\Response
   *   A Guzzle response.
   */
  protected function get($url) {
    $url = strtr($url, array(
      'feed://' => 'http://',
      'webcal://' => 'http://',
    ));

    $client = $this->getClient();

    $request = $client->get($url);

    return $request->send();
  }

  /**
   * Returns the configured HTTP client.
   *
   * @return Guzzle\Http\Client
   *   A Guzzle client.
   */
  protected function getClient() {
    if (!$this->client) {
      $options = array(
        Client::CURL_OPTIONS => array(
          CURLOPT_TIMEOUT => $this->timeout,
          CURLOPT_MAXREDIRS => $this->maxRedirects,
        ),
      );

      $this->client = new Client(NULL, $options);
    }

    return $this->client;
  }

}
