<?php

/**
 * Contains \Devour\Map\Map.
 */

namespace Devour\Map;

class NoopMap implements MapInterface {

  /**
   * {@inheritdoc}
   */
  public function getSourceField($target_field) {
    return $target_field;
  }

  /**
   * {@inheritdoc}
   */
  public function getTargetField($source_field) {
    return $source_field;
  }

}
