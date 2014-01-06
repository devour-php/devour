<?php

/**
 * @file
 * Contains \Devour\Payload\SimplePie.
 */

namespace Devour\Payload;

class SimplePie extends ParsedPayload {

  protected $title;

  /**
   * Sets the title.
   *
   * @param string $title
   *   The feed title.
   */
  public function setTitle($title) {
    $this->title = $title;
  }

}
