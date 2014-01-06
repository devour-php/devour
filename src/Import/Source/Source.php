<?php

/**
 * @file
 * Contains \Import\Source\Source.
 */

namespace Import\Source;

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
