<?php

/**
 * @file
 * Contains \Devour\Parser\ParserInterface.
 */

namespace Devour\Parser;

use Devour\Source\SourceInterface;
use Devour\Table\HasTableFactoryInterface;
use Psr\Http\Message\StreamInterface;

/**
 * The interface all parsers must implement.
 */
interface ParserInterface extends HasTableFactoryInterface {

  /**
   * Parses a stream.
   *
   * @param \Devour\Source\SourceInterface $source
   *   The source being imported.
   * @param \Psr\Http\Message\StreamInterface $stream
   *   The stream to use to obtain data to parse.
   *
   * @return \Devour\Table\TableInterface
   *   A table.
   */
  public function parse(SourceInterface $source, StreamInterface $stream);

}
