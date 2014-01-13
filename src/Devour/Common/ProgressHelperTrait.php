<?php

/**
 * @file
 * Contains \Devour\Common\ProgressHelperTrait.
 */

namespace Devour\Common;

use Devour\Source\SourceInterface;

/**
 * Quick implementation of ProgressInterface.
 */
trait ProgressHelperTrait {

  /**
   * The batch size.
   *
   * @var int
   */
  protected $limit;

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
    $this->limit = $limit;
  }

}
