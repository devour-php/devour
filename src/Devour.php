<?php

/**
 * @file
 * Contains \Devour\Devour.
 */

namespace Devour;

final class Devour {

  /**
   * The registered transporters.
   *
   * @var array
   */
  private static $transporters = [];

  /**
   * The registered parsers.
   *
   * @var array
   */
  private static $parsers = [];

  /**
   * The registered processors.
   *
   * @var array
   */
  private static $processors = [];

  /**
   * Registers some transporter classes.
   *
   * @param string[] $classes
   *   A list of transporter classes.
   */
  public static function registerTransporterClasses(array $classes) {
    self::$transporters += array_flip($classes);
  }

  /**
   * Registers some parser classes.
   *
   * @param string[] $classes
   *   A list of parser classes.
   */
  public static function registerParserClasses(array $classes) {
    self::$parsers += array_flip($classes);
  }

  /**
   * Registers some processor classes.
   *
   * @param string[] $classes
   *   A list of processor classes.
   */
  public static function registerProcessorClasses(array $classes) {
    self::$processors += array_flip($classes);
  }

  /**
   * Returns the list of registered transporter classes.
   *
   * @return array
   *   The list of registered transporters.
   */
  public static function getRegisteredTransporters() {
    return array_keys(self::$transporters);
  }

  /**
   * Returns the list of registered parser classes.
   *
   * @return array
   *   The list of registered parsers.
   */
  public static function getRegisteredParsers() {
    return array_keys(self::$parsers);
  }

  /**
   * Returns the list of registered processor classes.
   *
   * @return array
   *   The list of registered processors.
   */
  public static function getRegisteredProcessors() {
    return array_keys(self::$processors);
  }

  /**
   * Registers the default classes that come with Devour.
   */
  public static function registerDefaults() {
    static::registerTransporterClasses([
      'Devour\Transporter\Database',
      'Devour\Transporter\Stomp',
      'Devour\Transporter\Directory',
      'Devour\Transporter\File',
    ]);
    static::registerParserClasses([
      'Devour\Parser\Csv',
      'Devour\Parser\SimplePie',
    ]);
    static::registerProcessorClasses([
      'Devour\Processor\Pdo',
      'Devour\Processor\Printer',
      'Devour\Processor\CsvWriter',
    ]);
  }

}
