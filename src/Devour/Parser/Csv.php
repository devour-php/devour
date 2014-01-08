<?php

/**
 * @file
 * Contains \Devour\Parser\Csv.
 */

namespace Devour\Parser;

use Devour\ConfigurableInterface;
use Devour\Payload\PayloadInterface;
use Devour\ProgressInterface;
use Devour\Source\SourceInterface;

/**
 * A CSV parser.
 */
class Csv extends ParserBase implements ProgressInterface, ConfigurableInterface {

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
  public function parse(SourceInterface $source, PayloadInterface $payload) {
    $handle = $payload->getStream();
    // Resume where we left off.
    fseek($handle, $this->pointer);

    // Reset our counter.
    $this->linesRead = 0;

    // Initial load.
    if ($this->fileLength === NULL) {
      $this->fileLength = $payload->getSize();

      if ($this->hasHeader) {
        $this->header = $this->readLine($handle);
      }
    }

    $table = $this->getTableFactory()->create();

    while ($data = $this->getCsvLine($handle)) {

      if ($this->hasHeader) {
        $data = array_combine($this->header, $data);
      }
      $table->getNewRow()->setData($data);
    }

    $this->closeHandle($handle);

    return $table;
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
   * Sets the number of lines to parse at one time.
   *
   * @param int $limit
   *   The number of lines to parse.
   *
   * @return self
   *   The parser object for chaining.
   */
  public function setProcessLimit($limit) {
    $this->limit = $limit;
    return $this;
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
      return (float) $this->pointer / $this->fileLength;
    }

    return ProgressInterface::COMPLETE;
  }

}
