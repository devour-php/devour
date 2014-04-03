<?php

/**
 * @file
 * Contains \Devour\Importer\ImporterFactory.
 */

namespace Devour\Importer;

use Devour\Importer\ImporterBuilder;
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
   * @param array $config
   *   The configuration array.
   *
   * @return \Devour\Importer\ImporterInterface
   *   A new importer.
   *
   * @throws \RuntimeException
   *   Thrown if the configuration file is invalid.
   */
  public static function fromConfiguration(array $config) {

    // We will get an exception anyway if these do not exist, but this is a more
    // user-friendly way to do it.
    foreach (['transporter', 'processor'] as $part) {
      if (empty($config[$part]['class'])) {
        throw new \RuntimeException(sprintf('The %s class is required.', $part));
      }
    }

    $config = array_replace_recursive(static::defaultConfiguration(), $config);

    $builder = ImporterBuilder::get()
      ->setImporter($config['importer']['class'], $config['importer']['configuration'])
      ->setTransporter($config['transporter']['class'], $config['transporter']['configuration'])
      ->setProcessor($config['processor']['class'], $config['processor']['configuration'])
      ->setMap($config['map']['class'], $config['map']['configuration'])
      ->setTableClass($config['table']['class']);

    if ($config['parser']['class']) {
      $builder->setParser($config['parser']['class'], $config['parser']['configuration']);
    }

    return $builder->build();
  }

  /**
   * Returns the default configuration array.
   *
   * @return array
   *   The configuration defaults.
   */
  protected static function defaultConfiguration() {
    return [
      'importer' => [
        'class' => 'Devour\Importer\Importer',
        'configuration' => [],
      ],
      'transporter' => [
        'class' => '',
        'configuration' => [],
      ],
      'parser' => [
        'class' => '',
        'configuration' => [],
      ],
      'processor' => [
        'class' => '',
        'configuration' => [],
      ],
      'table' => [
        'class' => 'Devour\Table\Table',
      ],
      'map' => [
        'class' => 'Devour\Map\Map',
        'configuration' => [],
      ],
    ];
  }

}
