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

  /**
   * The raw source string.
   *
   * @var string
   */
  protected $source;

  /**
   * Constructs a new source object.
   *
   * @param string $source
   *   The raw source string.
   */
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
