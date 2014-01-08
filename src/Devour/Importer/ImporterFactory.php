<?php

/**
 * @file
 * Contains \Devour\Importer\ImporterFactory.
 */

namespace Devour\Importer;

use Devour\ConfigurableInterface;
use Devour\Table\TableFactory;
use Devour\Util\FileSystem;
use Symfony\Component\Yaml\Yaml;

/**
 * @todo
 */
class ImporterFactory {

  /**
   * Builds an import from a configuration file.
   *
   * @param string $filename
   *   The name of the configuration file.
   *
   * @return \Devour\Importer\ImporterInterface
   *   A new importer.
   *
   * @see \Devour\Importer\ImporterFactory::fromConfiguration()
   */
  public static function fromConfigurationFile($filename) {
    if (!FileSystem::checkFile($filename)) {
      throw new \RuntimeException(sprintf('The configuration file "%s" does not exist or is not readable.', $filename));
    }

    $configuration = Yaml::parse(file_get_contents($filename));

    if (!is_array($configuration)) {
      throw new \RuntimeException(sprintf('The configuration file "%s" is invalid.', $filename));
    }

    return static::fromConfiguration($configuration);
  }

  /**
   * Builds an import from a configuration array.
   *
   * @param array $configuration
   *   The configuration array.
   *
   * @return \Devour\Importer\ImporterInterface
   *   A new importer.
   *
   * @throws \RuntimeException
   *   Thrown if the configuration file is not readable.
   * @throws \Symfony\Component\Yaml\Exception\ParseException
   *   Thrown if the configuration file cannot be parsed.
   *
   * @todo Normalize Exceptions.
   */
  public static function fromConfiguration(array $configuration) {
    $parts = array();

    foreach (array('transporter', 'parser', 'processor') as $part) {
      if (empty($configuration[$part]['class'])) {
        throw new \RuntimeException(sprintf('The %s class is required.', $part));
      }

      $part_class = $configuration[$part]['class'];

      if (!class_exists($part_class)) {
        throw new \RuntimeException(sprintf('The "%s" class is unavailable.', $part_class));
      }

      if (is_subclass_of($part_class, 'Devour\ConfigurableInterface')) {
        $configuration[$part] += array('configuration' => array());
        $parts[$part] = $part_class::fromConfiguration($configuration[$part]['configuration']);
      }
      else {
        $parts[$part] = new $part_class();
      }
    }

    $parts['parser']->setTableFactory(static::getTableFactory($configuration));

    $importer_class = 'Devour\Importer\Importer';
    if (!empty($configuration['importer']['class'])) {
      $importer_class = $configuration['importer']['class'];
    }

    $importer_configuration = array();
    if (!empty($configuration['importer']['configuration'])) {
      $importer_configuration = $configuration['importer']['configuration'];
    }

    return new $importer_class($parts['transporter'], $parts['parser'], $parts['processor'], $configuration);
  }

  /**
   * Returns a table factory based on configuration.
   *
   * @param array $configuration
   *   The configuration.
   *
   * @return \Devour\Table\TableFactory
   *   A new table factory object.
   */
  protected static function getTableFactory(array $configuration) {
    $factory = new TableFactory();

    if (!empty($configuration['table']) && !empty($configuration['table']['class'])) {
      $factory->setTableClass($configuration['table']['class']);
    }

    if ($map = static::buildMap($configuration)) {
      $factory->setMap($map);
    }

    return $factory;
  }

  /**
   * Returns a map object based on configuration.
   *
   * @param array $configuration
   *   The configuration.
   *
   * @return \Devour\Map\MapInterface|bool
   *   A new map, or false if there is no map configuration.
   */
  protected static function buildMap(array $configuration) {
    if (empty($configuration['map'])) {
      return FALSE;
    }

    $class = 'Devour\Map\Map';
    if (!empty($configuration['map']['class'])) {
      $class = $configuration['map']['class'];
    }
    return new $class($configuration['map']['configuration']);
  }

}
