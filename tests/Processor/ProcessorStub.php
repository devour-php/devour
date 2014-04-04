<?php

/**
 * @file
 * Contains \Devour\Tests\Processor\ProcessorStub.
 */

namespace Devour\Tests\Processor;

use Devour\Processor\ProcessorInterface;
use Devour\Source\SourceInterface;
use Devour\Table\TableInterface;

/**
 * A stub processor for mocking.
 */
class ProcessorStub implements ProcessorInterface {

  /**
   * {@inheritdoc}
   */
  public function process(SourceInterface $source, TableInterface $table) {

  }

}
