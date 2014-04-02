<?php

/**
 * @file
 * Contains \Devour\Transporter\Guzzle.
 */

namespace Devour\Transporter;

use Devour\Common\ClearableInterface;
use Devour\Common\ProgressHelperTrait;
use Devour\Source\SourceInterface;
use GuzzleHttp\Plugin\Cache\CachePlugin;
use GuzzleHttp\Client;

/**
 * A transport that returns a stream via HTTP.
 */
class Guzzle extends Client implements TransporterInterface, ClearableInterface {

  use ProgressHelperTrait;

  /**
   * {@inheritdoc}
   */
  public function transport(SourceInterface $source) {
    return $this->get($source->getSource())->getBody();
  }

  /**
   * {@inheritdoc}
   */
  public static function fromConfiguration(array $configuration) {
    return new static($configuration);
  }

  /**
   * {@inheritdoc}
   */
  public function clear(SourceInterface $source) {
    // @todo Wait for update.

    // foreach ($this->getEmitter()->listeners('before') as $listener) {
    //   if ($listener[0] instanceof CachePlugin) {
    //     $this->createRequest('PURGE', $source->getSource())->send();
    //     break;
    //   }
    // }
  }

  /**
   * {@inheritdoc}
   */
  public function runInNewProcess() {
    return TRUE;
  }

}
