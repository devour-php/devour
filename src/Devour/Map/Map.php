<?php

/**
 * Contains \Devour\Map\Map.
 */

namespace Devour\Map;

use Devour\ConfigurableInterface;

class Map implements MapInterface, ConfigurableInterface {

  /**
   * A list of arrays from source => target.
   *
   * @var array
   */
  protected $map;

  /**
   * The position in the map.
   */
  protected $position = 0;

  /**
   * Constructs a Map object.
   *
   * @param array $map
   *   An array keyed from source => target.
   */
  public function __construct(array $map) {
    $this->map = $map;
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
  public function current() {
    return $this->map[$this->position][1];
  }

  /**
   * {@inheritdoc}
   */
  public function key() {
    return $this->map[$this->position][0];
  }

  /**
   * {@inheritdoc}
   */
  public function next() {
    $this->position++;
  }

  /**
   * {@inheritdoc}
   */
  public function rewind() {
    $this->position = 0;
  }

  /**
   * {@inheritdoc}
   */
  public function valid() {
    return isset($this->map[$this->position]);
  }

}
