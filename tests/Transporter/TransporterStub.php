<?php

/**
 * @file
 * Contains \Devour\Tests\Transporter\TransporterStub.
 */

namespace Devour\Tests\Transporter;

use Devour\Common\ProgressInterface;
use Devour\Source\SourceInterface;
use Devour\Tests\Stream\StreamStub;
use Devour\Transporter\TransporterInterface;

/**
 * A stub transporter implementation.
 */
class TransporterStub implements TransporterInterface {

  /**
   * {@inheritdoc}
   */
  public function transport(SourceInterface $source) {
    return new StreamStub();
  }

  /**
   * {@inheritdoc}
   */
  public function runInNewProcess() {
    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function progress(SourceInterface $source) {
    return ProgressInterface::COMPLETE;
  }

  /**
   * {@inheritdoc}
   */
  public function setProcessLimit($limit) {

  }

}
