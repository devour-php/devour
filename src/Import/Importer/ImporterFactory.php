<?php

/**
 * @file
 * Contains \Import\Importer\ImporterFactory.
 */

namespace Import\Importer;

use Import\ConfigurableInterface;
use Import\Util\FileSystem;
use Import\Util\Yaml;

/**
 * @todo
 */
class ImporterFactory {

  /**
   * @throws \RuntimeException
   *   Thrown if the configuration file is not readable.
   * @throws \Symfony\Component\Yaml\Exception\ParseException
   *   Thrown if the configuration file cannot be parsed.
   */
  public static function fromConfigurationFile($filename) {
    FileSystem::checkFile($filename);

    $configuration = Yaml::parse(file_get_contents($filename));
    return static::fromConfiguration($configuration);
  }

  /**
   * @todo
   */
  public static function fromConfiguration(array $configuration) {
    $parts = array();

    foreach (array('transport', 'parser', 'processor') as $part) {
      $part_class = $configuration[$part]['class'];

      if (is_subclass_of($part_class, '\Import\ConfigurableInterface')) {
        $parts[$part] = $part_class::fromConfiguration($configuration[$part]['configuration']);
      }
      else {
        $parts[$part] = new $part_class();
      }
    }

    $importer_class = $configuration['importer']['class'];

    return new $importer_class($parts['transport'], $parts['parser'], $parts['processor']);
  }

}
