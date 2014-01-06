<?php

/**
 * @file
 * Contains \Devour\Source\Source.
 */

namespace Devour\Source;

/**
 * @todo
 */
class Source implements SourceInterface {

  protected $source;

  public function __construct($source) {
    $this->source = $source;
  }

  /**
   * {@inheritdoc}
   */
  public function getSource() {
    return $this->source;
  }

  /**
   * {@inheritdoc}
   */
  public function __toString() {
    return $this->getSource();
  }

}
