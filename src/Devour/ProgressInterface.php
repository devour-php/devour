<?php

/**
 * @file
 * Contains \Devour\ProgressInterface.
 */

namespace Devour;

/**
 * Allows progress reporting.
 *
 * Transports, parsers, and processors that implement this interface can do
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
  public function progress();

}
