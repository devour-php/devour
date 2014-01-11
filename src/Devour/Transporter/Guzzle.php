<?php

/**
 * @file
 * Contains \Devour\Transporter\Guzzle.
 */

namespace Devour\Transporter;

use Devour\Common\ClearableInterface;
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
    return $request->send()->getBody();
  }

  /**
   * {@inheritdoc}
   */
  public static function fromConfiguration(array $configuration) {
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

  /**
   * {@inheritdoc}
   */
  public function runInNewProcess() {
    return TRUE;
  }

}
