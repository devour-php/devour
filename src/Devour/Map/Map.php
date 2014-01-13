<?php

/**
 * @file
 * Contains \Devour\Map\Map.
 */

namespace Devour\Map;

use Devour\Common\ConfigurableInterface;

/**
 * Maps source fields to target fields. The map configuration structure:
 *
 * @code
 * array(
 *   array('source 1', 'target 1'),
 *   array('source 2', 'target 2'),
 * )
 * @endcode
 *
 * This allows mapping the same source to multiple targets, or multiple sources
 * to the same target.
 */
class Map implements MapInterface, ConfigurableInterface {

  /**
   * A list of arrays with array('source', 'target').
   *
   * @var array
   */
  protected $map;

  /**
   * The position in the map.
   *
   * @var int
   */
  protected $position = 0;

  /**
   * The number of items in the map.
   *
   * @var int
   */
  protected $count;

  /**
   * Constructs a Map object.
   *
   * @param array $map
   *   A list of arrays with array('source', 'target').
   */
  public function __construct(array $map) {
    // Ensure we have a zero indexed array with no missing keys.
    $this->map = array_values($map);
    $this->count = count($this->map);
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
    return $this->position < $this->count;
  }

}
