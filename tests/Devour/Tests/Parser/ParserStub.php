<?php

/**
 * @file
 * Contains \Devour\Tests\Parser\ParserStub.
 */

namespace Devour\Tests\Parser;

use Devour\Parser\ParserBase;
use Devour\Payload\PayloadInterface;

/**
 * A minimal parser.
 */
class ParserStub extends ParserBase {

  /**
   * {@inheritdoc}
   */
  public function parse(PayloadInterface $payload) {
    return $this->getTableFactory()->create();
  }

}
