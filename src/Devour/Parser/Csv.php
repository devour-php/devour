<?php

/**
 * @file
 * Contains \Devour\Parser\Csv.
 */

namespace Devour\Parser;

use Devour\Common\ConfigurableInterface;
use Devour\Common\ProgressInterface;
use Devour\Source\SourceInterface;
use Devour\Table\HasTableFactoryTrait;
use Guzzle\Stream\StreamInterface;

/**
 * A CSV parser.
 *
 * @todo Handle encoding.
 */
class Csv implements ParserInterface, ProgressInterface, ConfigurableInterface {

  use HasTableFactoryTrait;

  /**
   * The length of the longest line in the file.
   *
   * Setting this can be a performance improvement. Zero means to limit.
   *
   * @var int
   */
  protected $length = 0;

  /**
   * The field delimiter (one character only).
   *
   * @var string
   */
  protected $delimiter = ',';

  /**
   * The field enclosure (one character only).
   *
   * @var string
   */
  protected $enclosure = '"';

  /**
   * The escape character (one character only).
   *
   * @var string
   */
  protected $escape = '\\';

  /**
   * Whether the file contains a header.
   *
   * @var bool
   */
  protected $hasHeader = FALSE;

  /**
   * The number of lines to read in one batch.
   *
   * @var int
   */
  protected $limit = 50;

  /**
   * The number of lines that have been read during the current batch.
   *
   * @var int
   */
  protected $linesRead;

  /**
   * What fgetcsv() considers an empty line.
   *
   * We skip empty lines. Store it so that we aren't creating a bunch of empty
   * arrays.
   *
   * @var array
   */
  protected static $emptyLine = array(NULL);

  /**
   * {@inheritdoc}
   */
  public static function fromConfiguration(array $configuration) {
    // If we wrap this array, PHPUnit says we didn't cover the last line.
    // @todo Figure out why.
    $configuration += array('has_header' => FALSE, 'length' => 0, 'delimiter' => ',', 'enclosure' => '"', 'escape' => '\\');

    $parser = new static();
    $parser->setHasHeader((bool) $configuration['has_header'])
           ->setLineLength((int) $configuration['length'])
           ->setDelimiter($configuration['delimiter'])
           ->setEnclosure($configuration['enclosure'])
           ->setEscape($configuration['escape']);

    return $parser;
  }

  /**
   * {@inheritdoc}
   */
  public function parse(SourceInterface $source, StreamInterface $stream) {
    $state = $source->getState($this);
    $handle = $stream->getStream();

    // Resume where we left off.
    fseek($handle, $state->pointer);

    // Initial load.
    if ($state->isFirstRun()) {
      $state->fileLength = $stream->getSize();

      if ($this->hasHeader) {
        $state->header = $this->readLine($handle);
      }
    }

    // Reset our counter.
    $this->linesRead = 0;

    $table = $this->getTableFactory()->create();

    while ($data = $this->getCsvLine($handle)) {

      if ($this->hasHeader) {
        $data = array_combine($state->header, $data);
      }
      $table->getNewRow()->setData($data);
    }

    $state->pointer = ftell($handle);

    return $table;
  }

  /**
   * Reads a CSV line from a handle.
   *
   * This increments the counter.
   *
   * @param resource $handle
   *   A file handle.
   *
   * @return array|bool
   *   An indexed array containing the fields read or false on error.
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
   * @return array|bool
   *   An indexed array containing the fields read or false on error.
   */
  protected function readLine($handle) {

    do {
      $data = fgetcsv($handle, $this->length, $this->delimiter, $this->enclosure, $this->escape);
    } while ($data === static::$emptyLine);

    return $data;
  }

  /**
   * {@inheritdoc}
   */
  public function progress(SourceInterface $source) {
    $state = $source->getState($this);

    if (!empty($state->fileLength)) {
      return (float) $state->pointer / $state->fileLength;
    }

    return ProgressInterface::COMPLETE;
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
   * Sets the length of the longest line in the file.
   *
   * Setting this can be a performance improvement. Zero means to limit.
   *
   * @param int $length
   *   The length of the longest line.
   *
   * @return self
   *   The parser object for chaining.
   *
   * @see fgetcsv()
   */
  public function setLineLength($length) {
    $this->length = $length;
    return $this;
  }

  /**
   * Sets the delimiter character (one character only).
   *
   * @param string $delimiter
   *   The delimiter character.
   *
   * @return self
   *   The parser object for chaining.
   *
   * @see fgetcsv()
   */
  public function setDelimiter($delimiter) {
    $this->delimiter = $delimiter;
    return $this;
  }

  /**
   * Sets the enclosure character (one character only).
   *
   * @param string $enclosure
   *   The enclosure character.
   *
   * @return self
   *   The parser object for chaining.
   *
   * @see fgetcsv()
   */
  public function setEnclosure($enclosure) {
    $this->enclosure = $enclosure;
    return $this;
  }

  /**
   * Sets the escape character (one character only).
   *
   * @param string $escape
   *   The escape character.
   *
   * @return self
   *   The parser object for chaining.
   *
   * @see fgetcsv()
   */
  public function setEscape($escape) {
    $this->escape = $escape;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function setProcessLimit($limit) {
    $this->limit = $limit;
    return $this;
  }

}
