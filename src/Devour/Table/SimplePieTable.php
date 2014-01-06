<?php

/**
 * @file
 * Contains \Devour\Table\SimplePieTable.
 */

namespace Devour\Table;

class SimplePieTable extends Table {

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
