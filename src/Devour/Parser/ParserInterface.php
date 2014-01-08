<?php

/**
 * @file
 * Contains \Devour\Parser\ParserInterface.
 */

namespace Devour\Parser;

use Devour\Payload\PayloadInterface;
use Devour\Source\SourceInterface;
use Devour\Table\TableFactory;

/**
 * The interface all parsers must implement.
 */
interface ParserInterface {

  /**
   * Parses a raw payload.
   *
   * @param \Devour\Source\SourceInterface $source
   *   The source being imported.
   * @param \Devour\Payload\PayloadInterface $payload
   *   The raw payload.
   *
   * @return \Devour\Table\TableInterface
   *   A parsed payload.
   */
  public function parse(SourceInterface $source, PayloadInterface $payload);

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
