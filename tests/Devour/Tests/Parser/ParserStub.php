<?php

/**
 * @file
 * Contains \Devour\Tests\Parser\ParserStub.
 */

namespace Devour\Tests\Parser;

use Devour\Parser\ParserBase;
use Devour\Source\SourceInterface;
use Guzzle\Stream\StreamInterface;

/**
 * A minimal parser.
 */
class ParserStub extends ParserBase {

  /**
   * {@inheritdoc}
   */
  public function parse(SourceInterface $source, StreamInterface $stream) {
    return $this->getTableFactory()->create();
  }

}
