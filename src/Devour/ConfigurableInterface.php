<?php

/**
 * @file
 * Contains \Devour\ConfigurableInterface.
 */

namespace Devour;

/**
 * Factory interface for configurable thingies.
 */
interface ConfigurableInterface {

  /**
   * Returns a new object based on some configuration.
   *
   * @param array $configuration
   *   The configuration array.
   *
   * @return object
   *   Some new object based on configuration.
   *
   * @throws \Devour\Exception\ConfigurationException
   *   Thrown when there is a fatal configuration error.
   */
  public static function fromConfiguration(array $configuration);

}
