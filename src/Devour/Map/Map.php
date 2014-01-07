<?php

/**
 * Contains \Devour\Map\Map.
 */

namespace Devour\Map;

class Map implements MapInterface {

  /**
   * A map from target to source.
   *
   * @var array
   */
  protected $targetToSource;

  /**
   * A map from source to target.
   *
   * @var array
   */
  protected $sourceToTarget;

  /**
   * Constructs a new Map object.
   *
   * @param array $map
   *   A array keyed from source => target.
   */
  public function __construct(array $map) {
    $this->targetToSource = array_reverse($map);
    $this->sourceToTarget = $map;
  }

  /**
   * {@inheritdoc}
   */
  public function getSourceField($target_field) {
    if (isset($this->targetToSource[$target_field])) {
      return $this->targetToSource[$target_field];
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getTargetField($source_field) {
    if (isset($this->sourceToTarget[$source_field])) {
      return $this->sourceToTarget[$source_field];
    }
  }

}
