<?php

/**
 * @file
 * Contains \Devour\Processor\CsvWriter.
 */

namespace Devour\Processor;

use Devour\Common\ClearableInterface;
use Devour\Common\ConfigurableInterface;
use Devour\Row\RowInterface;
use Devour\Source\SourceInterface;
use Devour\Table\TableInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;

/**
 * Writes data to a CSV file.
 */
class CsvWriter implements ProcessorInterface, ConfigurableInterface, ClearableInterface, LoggerAwareInterface {

  use LoggerAwareTrait;

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
   * @param string $directory
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

    if (!is_dir($directory)) {
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

  /**
   * Returns the file handle to write to.
   *
   * @param \Devour\Source\SourceInterface $source
   *   The current source being processed.
   *
   * @return resource
   *   A file handle.
   */
  protected function getHandle(SourceInterface $source) {
    $filename = $this->getFileName($source);
    $last = error_reporting(0);
    $handle = fopen($filename, $this->mode);
    error_reporting($last);

    if ($handle === FALSE) {
      throw new \RuntimeException(sprintf('Error opening %s.', $filename));
    }

    if ($this->header) {
      $this->writeHeader($handle, $filename);
    }

    return $handle;
  }

  /**
   * Writes the header to the csv file.
   *
   * @param resource $handle
   *   The file handle to write to.
   * @param string $filename
   *   The filename to write to.
   */
  protected function writeHeader($handle, $filename) {
    if (filesize($filename) === 0 || $this->mode != 'a') {
      fputcsv($handle, $this->header, $this->delimeter, $this->enclosure);
    }
  }

  /**
   * Returns the name of the file to write to.
   *
   * @param \Devour\Source\SourceInterface $source
   *   The current source.
   *
   * @return string
   *   The file name.
   */
  protected function getFileName(SourceInterface $source) {
    $source = str_replace('/', '_', $source);
    return "{$this->directory}/$source.csv";
  }

  /**
   * @param resource $handle
   *   The file handle to write to.
   * @param \Devour\Row\RowInterface $row
   *   The row to write to the file.
   */
  protected function processRow($handle, RowInterface $row) {
    fputcsv($handle, $row->getData(), $this->delimeter, $this->enclosure);
  }

  /**
   * {@inheritdoc}
   */
  public function clear(SourceInterface $source) {
    $filename = $this->getFileName($source);
    if (is_file($filename)) {
      unlink($filename);
    }
  }

}
