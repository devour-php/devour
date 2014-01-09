<?php

/**
 * @file
 * Contains \Devour\Transporter\Guzzle.
 */

namespace Devour\Transporter;

use Devour\ClearableInterface;
use Devour\Source\SourceInterface;
use Guzzle\Plugin\Cache\CachePlugin;
use Guzzle\Service\Client;

/**
 * A transport that returns a stream via HTTP.
 */
class Guzzle extends Client implements TransporterInterface, ClearableInterface {

  /**
   * {@inheritdoc}
   */
  public function transport(SourceInterface $source) {
    $request = $this->get($source->getSource());

    // Guzzle use's php://temp as a temporary file. That is awesome, but for our
    // multiprocessing, we need a real file path.
    if ($this->getConfig('stream_to_file')) {
      $request->setResponseBody(tempnam(sys_get_temp_dir(), 'devour_'));
    }

    return $request->send()->getBody();
  }

  /**
   * {@inheritdoc}
   */
  public static function fromConfiguration(array $configuration) {
    $configuration += array('stream_to_file' => TRUE);
    return static::factory($configuration);
  }

  /**
   * {@inheritdoc}
   */
  public function clear(SourceInterface $source) {
    foreach ($this->getEventDispatcher()->getListeners('request.before_send') as $listener) {
      if ($listener instanceof CachePlugin) {
        $this->createRequest('PURGE', $source->getSource())->send();
        break;
      }
    }
  }

}
