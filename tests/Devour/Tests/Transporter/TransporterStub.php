<?php

/**
 * @file
 * Contains \Devour\Tests\Transporter\TransporterStub.
 */

namespace Devour\Tests\Transporter;

use Devour\Payload\FilePayload;
use Devour\Source\SourceInterface;
use Devour\Transporter\TransporterInterface;

/**
 * A transport that fetches a payload via a local file.
 */
class TransporterStub implements TransporterInterface {

  /**
   * {@inheritdoc}
   */
  public function transport(SourceInterface $source) {
    return new FilePayload('');
  }

}
