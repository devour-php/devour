<?php

/**
 * @file
 * Contains \Import\Payload\ParsedPayloadInterface.
 */

namespace Import\Payload;

interface ParsedPayloadInterface {

  /**
   * Returns the first row, removing it.
   *
   * @return \Import\Row\RowInterface.
   *   A row object.
   */
  public function shiftRow();

}
