<?php

/**
 * @file
 * Contains \Devour\Util\Configuration.
 */

namespace Devour\Util;

use Devour\Common\Exception\ConfigurationException;

/**
 * Helper functions for dealing with configuration.
 */
class Configuration {

  /**
   * Validates a configuration array, adding default values.
   *
   * @param array $configuration
   *   The configuration to validate.
   * @param array $defaults
   *   The default values to provide.
   * @param array $required
   *   (optional) A list of required configuration keys.
   *
   * @return array
   *  The configuration array with the default values added.
   *
   * @throws \Devour\Common\Exception\ConfigurationException
   *   Thrown when a required key is missing.
   */
  public static function validate(array $configuration, array $defaults, array $required = []) {
    $configuration = array_replace_recursive($defaults, $configuration);
    foreach ($required as $key) {
      if (!isset($configuration[$key])) {
        throw new ConfigurationException(sprintf('The field "%s" is required.', $key));
      }
    }

    return $configuration;
  }

}
