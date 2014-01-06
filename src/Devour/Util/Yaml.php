<?php

/**
 * @file
 * Contains \Devour\Util\Yaml.
 */

namespace Devour\Util;

/**
 * Wrapper class around Yaml parsing.
 */
class Yaml {

  /**
   * Parses a Yaml string.
   *
   * This checks for different Yaml parsers, using the first one found.
   *
   * @param string $string
   *   A Yaml string.
   *
   * @return array
   *   A PHP array.
   */
  public static function parse($string) {
    if (extension_loaded('yaml')) {
      return yaml_parse($string);
    }
    elseif (class_exists('\Symfony\Component\Yaml\Yaml')) {
      $class = '\Symfony\Component\Yaml\Yaml';
      return $class::parse($string);
    }
    elseif (class_exists('\Spyc')) {
      $class = '\Spyc';
      return $class::YAMLLoadString($string);
    }

    throw new \RuntimeException('No Yaml parser found. Install the Symfony Yaml component.');
  }

}
