<?php

/**
 * @file
 * Contains \Devour\Source\SourceInterface.
 */

namespace Devour\Source;

/**
 * The interface all sources need to implement.
 *
 * @todo
 */
interface SourceInterface {

  /**
   * Returns the source address used to transport the payload.
   *
   * @return string
   *   Some kind of address string, for example, a web address, or a file path.
   */
  public function getSource();

  /**
   * Returns the string representation of this source.
   *
   * Most implementations will just return self::getSource().
   *
   * @return string
   *   Some kind of address string, for example, a web address, or a file path.
   */
  public function __toString();

}
