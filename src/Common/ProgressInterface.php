<?php

/**
 * @file
 * Contains \Devour\Common\ProgressInterface.
 */

namespace Devour\Common;

use Devour\Source\SourceInterface;

/**
 * Allows progress reporting.
 *
 * Transporters, parsers, and processors that implement this interface can do
 * batching or multi-process importing.
 */
interface ProgressInterface {

  /**
   * Indicated completed progress.
   *
   * @var float
   */
  const COMPLETE = 1.0;

  /**
   * Returns the progress for a part of the import process.
   *
   * @return float
   *   A number between 0 and 1.
   */
  public function progress(SourceInterface $source);

  /**
   * Sets the number of lines to parse at one time.
   *
   * @param int $limit
   *   The number of lines to parse.
   *
   * @return self
   *   The object for chaining.
   */
  public function setProcessLimit($limit);

}
