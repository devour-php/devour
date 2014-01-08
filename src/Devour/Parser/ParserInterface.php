<?php

/**
 * @file
 * Contains \Devour\Parser\ParserInterface.
 */

namespace Devour\Parser;

use Devour\Source\SourceInterface;
use Devour\Table\TableFactory;
use Guzzle\Stream\StreamInterface;

/**
 * The interface all parsers must implement.
 */
interface ParserInterface {

  /**
   * Parses a stream.
   *
   * @param \Devour\Source\SourceInterface $source
   *   The source being imported.
   * @param \Guzzle\Stream\StreamInterface $stream
   *   The stream to use to obtain data to parse.
   *
   * @return \Devour\Table\TableInterface
   *   A table.
   */
  public function parse(SourceInterface $source, StreamInterface $stream);

  /**
   * Set the table factory.
   *
   * @param \Devour\Table\TableFactory $table_factory
   *   The table factory.
   */
  public function setTableFactory(TableFactory $table_factory);

  /**
   * Returnds the table factory.
   *
   * @return \Devour\Table\TableFactory
   *   The table factory.
   */
  public function getTableFactory();

}
