<?php

/**
 * @file
 * Contains \Devour\Devour.
 */

namespace Devour;

final class Devour {

  private static $transporters = array();
  private static $parsers = array();
  private static $processors = array();

  public static function registerTransporterClasses(array $classes) {
    static::$transporters += array_flip($classes);
  }

  public static function registerParserClasses(array $classes) {
    static::$parsers += array_flip($classes);
  }

  public static function registerProcessorClasses(array $classes) {
    static::$processors += array_flip($classes);
  }

  public static function getRegisteredTransporters() {
    return array_keys(static::$transporters);
  }

  public static function getRegisteredParsers() {
    return array_keys(static::$parsers);
  }

  public static function getRegisteredProcessors() {
    return array_keys(static::$processors);
  }

  public static function registerDefaults() {
    static::registerTransporterClasses(array(
      'Devour\Transporter\Database',
      'Devour\Transporter\Guzzle',
      'Devour\Transporter\Stomp',
      'Devour\Transporter\Directory',
      'Devour\Transporter\File',
    ));
    static::registerParserClasses(array(
      'Devour\Parser\Csv',
      'Devour\Parser\SimplePie',
    ));
    static::registerProcessorClasses(array(
      'Devour\Processor\Pdo',
      'Devour\Processor\Printer',
      'Devour\Processor\CsvWriter',
    ));
  }

}
