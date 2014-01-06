<?php

/**
 * @file
 * Contains \Devour\Table\SimplePie.
 */

namespace Devour\Table;

class SimplePie extends Table {

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
