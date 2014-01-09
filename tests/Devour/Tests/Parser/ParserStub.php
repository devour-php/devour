<?php

/**
 * @file
 * Contains \Devour\Tests\Parser\ParserStub.
 */

namespace Devour\Tests\Parser;

use Devour\Parser\ParserInterface;
use Devour\Source\SourceInterface;
use Devour\Table\HasTableFactoryTrait;
use Guzzle\Stream\StreamInterface;

/**
 * A minimal parser.
 */
class ParserStub implements ParserInterface {

  use HasTableFactoryTrait;

  /**
   * {@inheritdoc}
   */
  public function parse(SourceInterface $source, StreamInterface $stream) {
    return $this->getTableFactory()->create();
  }

}
