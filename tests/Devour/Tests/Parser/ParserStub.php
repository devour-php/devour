<?php

/**
 * @file
 * Contains \Devour\Tests\Parser\ParserStub.
 */

namespace Devour\Tests\Parser;

use Devour\Parser\ParserInterface;
use Devour\Payload\PayloadInterface;
use Devour\Table\Table;

/**
 * A CSV parser.
 */
class ParserStub implements ParserInterface {

  /**
   * {@inheritdoc}
   */
  public function parse(PayloadInterface $payload) {
    return new Table();
  }

}
