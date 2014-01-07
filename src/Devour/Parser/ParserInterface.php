<?php

/**
 * @file
 * Contains \Devour\Parser\ParserInterface.
 */

namespace Devour\Parser;

use Devour\Payload\PayloadInterface;
use Devour\Table\TableFactory;

/**
 * The interface all parsers must implement.
 */
interface ParserInterface {

  /**
   * Parses a raw payload.
   *
   * @param \Devour\Payload\PayloadInterface $payload
   *   The raw payload.
   *
   * @return \Devour\Table\TableInterface
   *   A parsed payload.
   */
  public function parse(PayloadInterface $payload);

  public function setTableFactory(TableFactory $table_factory);

  public function getTableFactory();

}
