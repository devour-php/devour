<?php

/**
 * @file
 * Contains \Devour\Processor\CsvWriter.
 */

namespace Devour\Processor;

use Devour\ClearableInterface;
use Devour\ConfigurableInterface;
use Devour\Row\RowInterface;
use Devour\Source\SourceInterface;
use Devour\Table\TableInterface;
use Devour\Util\FileSystem;

/**
 * Writes data to a CSV file.
 */
class CsvWriter implements ProcessorInterface, ConfigurableInterface, ClearableInterface {

  /**
   * The directory to store the files in.
   *
   * @var string
   */
  protected $directory;

  /**
   * The header.
   *
   * @var array
   */
  protected $header;

  /**
   * The mode to use for file writing.
   *
   * @var string
   */
  protected $mode;

  /**
   * The field delimiter (one character only).
   *
   * @var string
   */
  protected $delimeter;

  /**
   * The field enclosure (one character only).
   *
   * @var string
   */
  protected $enclosure;

  /**
   * Constructs a CsvWriter object.
   *
   * @param $directory
   *   The directory to store the files in.
   * @param array $header
   *   (optional) A header array. Defaults to null.
   * @param string $mode
   *   (optional) The write mode to use. Either 'a' for append, or 'w' for
   *   write. Defaults to 'a'.
   * @param string $delimeter
   *   (optional) The field delimiter (one character only). Defaults to ','.
   * @param string $enclosure
   *   (optional) The field enclosure (one character only). Defaults to '"'.
   */
  public function __construct($directory, array $header = NULL, $mode = 'a', $delimeter = ',', $enclosure = '"') {
    $this->directory = $directory;
    $this->header = $header;
    $this->mode = trim($mode, '+');
    $this->delimeter = $delimeter;
    $this->enclosure = $enclosure;

    if (!FileSystem::checkDirectory($directory)) {
      mkdir($directory);
    }
  }

  /**
   * {@inheritdoc}
   */
  public static function fromConfiguration(array $config) {
    if (empty($config['directory'])) {
      throw new \RuntimeException('The directory parameter is required for CsvWriter.');
    }

    $config += array('header' => NULL, 'mode' => 'a', 'delimeter' => ',', 'enclosure' => '"');
    return new static($config['directory'], $config['header'], $config['mode'], $config['delimeter'], $config['enclosure']);
  }

  /**
   * {@inheritdoc}
   */
  public function process(SourceInterface $source, TableInterface $table) {
    $handle = $this->getHandle($source);

    foreach ($table as $row) {
      $this->processRow($handle, $row);
    }

    fclose($handle);
  }

  protected function getHandle(SourceInterface $source) {
    $filename = $this->getFileName($source);
    $handle = fopen($filename, $this->mode);

    if (!$this->header) {
      return $handle;
    }

    if (filesize($filename) === 0 || $this->mode != 'a') {
      fputcsv($handle, $this->header);
    }

    return $handle;
  }

  protected function getFileName(SourceInterface $source) {
    $source = str_replace('/', '_', $source);
    return "{$this->directory}/$source.csv";
  }

  /**
   * {@inheritdoc}
   */
  protected function processRow($handle, RowInterface $row) {
    fputcsv($handle, $row->getData(), $this->delimeter, $this->enclosure);
  }

  /**
   * {@inheritdoc}
   */
  public function clear(SourceInterface $source) {
    $filename = $this->getFileName($source);

    if (file_exists($filename)) {
      unlink($filename);
    }
  }

}
