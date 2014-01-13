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
    foreach (array('transporter', 'processor') as $part) {
      if (empty($config[$part]['class'])) {
        throw new \RuntimeException(sprintf('The %s class is required.', $part));
      }
    }

    $config = array_replace_recursive(static::defaultConfiguration(), $config);

    $builder = ImporterBuilder::get($config['importer']['configuration'], $config['importer']['class'])
      ->setTransporter($config['transporter']['class'], $config['transporter']['configuration'])
      ->setProcessor($config['processor']['class'], $config['processor']['configuration'])
      ->setTableClass($config['table']['class']);

    if (!empty($config['map']['class']) || $config['map']['configuration']) {
      $config['map'] += array('class' => 'Devour\Map\Map');
      $builder->setMap($config['map']['class'], $config['map']['configuration']);
    }

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
    return array(
      'importer' => array(
        'class' => 'Devour\Importer\Importer',
        'configuration' => array(),
      ),
      'transporter' => array(
        'class' => '',
        'configuration' => array(),
      ),
      'parser' => array(
        'class' => '',
        'configuration' => array(),
      ),
      'processor' => array(
        'class' => '',
        'configuration' => array(),
      ),
      'table' => array(
        'class' => 'Devour\Table\Table',
      ),
      'map' => array(
        'configuration' => array(),
      ),
    );
  }

}
