<?php

/**
 * Contains \Devour\Map\Map.
 */

namespace Devour\Map;

interface MapInterface {

  /**
   * Returns the source field given the target field.
   *
   * @param string $target_field
   *   The name of the target field.
   *
   * @return string
   *   The name of the source field.
   */
  public function getSourceField($target_field);

  /**
   * Returns the target field given the source field.
   *
   * @param string $source_field
   *   The name of the source field.
   *
   * @return string
   *   The name of the target field.
   */
  public function getTargetField($source_field);

}
