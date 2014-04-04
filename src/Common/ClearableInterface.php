<?php

/**
 * @file
 * Contains \Devour\Common\ClearableInterface.
 */

namespace Devour\Common;

use Devour\Source\SourceInterface;

/**
 * Importers can execute a clear stage. This can be used to remove data, or
 * clear caches.
 */
interface ClearableInterface {

  /**
   * Clears state for a given source.
   *
   * @param \Devour\Source\SourceInterface $source
   *   The source to clear state for.
   *
   * @return void
   */
  public function clear(SourceInterface $source);

}
