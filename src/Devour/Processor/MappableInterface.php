<?php

/**
 * @ilfe
 * Contains \Devour\Processor\MappableInterface.
 */

namespace Devour\Processor;

use Devour\Map\MapInterface;

/**
 * Defines a processor that accepts a map.
 */
interface MappableInterface {

  /**
   * Sets a map.
   *
   * @param \Devour\Map\MapInterface $map
   *   The map to set.
   */
  public function setMap(MapInterface $map);

  /**
   * Returns the map.
   *
   * @return \Devour\Map\MapInterface
   *   The map.
   */
  public function getMap();

}
