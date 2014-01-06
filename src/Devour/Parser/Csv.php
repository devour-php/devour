<?php

/**
 * @file
 * Contains \Devour\Parser\Csv.
 */

namespace Devour\Parser;

use Devour\ConfigurableInterface;
use Devour\Table\CsvTable;
use Devour\Payload\PayloadInterface;
use Devour\ProgressInterface;

/**
 * A CSV parser.
 */
class Csv implements ParserInterface, ProgressInterface, ConfigurableInterface {

  protected $length = 0;

  protected $delimiter = ',';

  protected $enclosure = '"';

  protected $escape = '\\';

  protected $pointer = 0;

  protected $header;

  protected $hasHeader = FALSE;

  protected $limit = 50;

  protected $linesRead;

  protected $emptyLine;

  protected $fileLength;

  /**
   * Constructs a new Csv object.
   */
  public function __construct() {
    $this->emptyLine = array(NULL);
  }

  /**
   * {@inheritdoc}
   */
  public static function fromConfiguration(array $configuration) {
    $parser = new static();
    if (!empty($configuration['has_header'])) {
      $parser->setHasHeader(TRUE);
    }

    return $parser;
  }

  /**
   * {@inheritdoc}
   *
   * @todo Handle encoding.
   */
  public function parse(PayloadInterface $payload) {
    $filepath = $payload->getPath();

    $handle = $this->openHandle($filepath);

    // Initial load.
    if ($this->fileLength === NULL) {
      $this->fileLength = filesize($filepath);

      if ($this->hasHeader) {
        $this->header = $this->readLine($handle);
      }
    }

    $result = new CsvTable();

    if ($this->hasHeader) {
      $result->setHeader($this->header);
    }

    while ($data = $this->getCsvLine($handle)) {
      $result->addRow($data);
    }

    $this->closeHandle($handle);

    return $result;
  }

  /**
   * Sets whether or not this CSV file contains a header row.
   *
   * @param bool $has_header
   *   True if the file contains a header, false if not.
   *
   * @return self
   *   The parser object for chaining.
   */
  public function setHasHeader($has_header) {
    $this->hasHeader = $has_header;
    return $this;
  }

  /**
   * Returns an open file handle.
   *
   * @param string $filepath
   *   The path to a file.
   *
   * @return resource
   *   An open file handle.
   *
   * @throws \RuntimeException
   *   Thrown if the file cannot be read.
   */
  protected function openHandle($filepath) {

    if (!is_file($filepath) || !is_readable($filepath)) {
      throw new \RuntimeException('The CSV file could not be read.');
    }

    $handle = fopen($filepath, 'r');

    // Resume where we left off.
    fseek($handle, $this->pointer);

    // Reset our counter.
    $this->linesRead = 0;

    return $handle;
  }

  /**
   * Closes a file handle.
   *
   * @param resource $handle
   *   An open file handle.
   */
  protected function closeHandle($handle) {
    $this->pointer = ftell($handle);
    fclose($handle);
  }

  /**
   * Reads a CSV line from a handle.
   *
   * This increments the counter.
   *
   * @param resource $handle
   *   A file handle.
   *
   * @return array|false
   *   An indexed array containing the fields read or FALSE on other errors.
   *
   * @see self::readLine()
   */
  protected function getCsvLine($handle) {
    if ($this->linesRead <= $this->limit) {
      $this->linesRead++;
      return $this->readLine($handle);
    }

    return FALSE;
  }

  /**
   * Reads a single line in a CSV file.
   *
   * This will skip empty lines.
   *
   * @param resource $handle
   *   An open file handle.
   *
   * @return array|false
   *   An indexed array containing the fields read or FALSE on other errors.
   */
  protected function readLine($handle) {

    do {
      $data = fgetcsv($handle, $this->length, $this->delimiter, $this->enclosure, $this->escape);
    } while ($data === $this->emptyLine);

    return $data;
  }

  /**
   * {@inheritdoc}
   */
  public function progress() {
    if ($this->fileLength) {
      return $this->pointer / $this->fileLength;
    }

    return ProgressInterface::COMPLETE;
  }

}
