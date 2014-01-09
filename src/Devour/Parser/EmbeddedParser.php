<?php

/**
 * @file
 * Contains \Devour\Parser\EmbeddedParser.
 */

namespace Devour\Parser;

use Devour\Parser\ParserBase;
use Devour\Source\SourceInterface;
use Devour\Table\FixedTableFactory;
use Guzzle\Stream\Stream;
use Guzzle\Stream\StreamInterface;

/**
 * A Parser that allows another parser to be used to parse the rows.
 */
class EmbeddedParser extends ParserBase {

  protected $parser;

  protected $subParser;

  protected $embeddedField;

  public function __construct(ParserInterface $parser, ParserInterface $sub_parser, $embedded_field) {
    $this->parser = $parser;
    $this->subParser = $sub_parser;
    $this->embeddedField = $embedded_field;
  }

  public function parse(SourceInterface $source, StreamInterface $stream) {
    $table = $this->parser->parse($source, $stream);

    // Write the embedded field to a new stream and pass it to the sub-parser.
    $handle = fopen('php://temp', 'r+');

    fwrite($handle, $table->getField($this->embeddedField));
    fseek($handle, 0);
    $sub_stream = new Stream($handle);

    $this->subParser->setTableFactory(new FixedTableFactory($table));

    $this->subParser->parse($source, $sub_stream);

    return $table;
  }

}
