<?php

/**
 * @file
 * Contains \Devour\Payload\ParsedPayloadInterface.
 */

namespace Devour\Payload;

interface ParsedPayloadInterface {

  /**
   * Returns the first row, removing it.
   *
   * @return \Devour\Row\RowInterface.
   *   A row object.
   */
  public function shiftRow();

}
