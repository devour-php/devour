<?php

/**
 * @file
 * Contains \Devour\Table\SimplePieTable.
 */

namespace Devour\Table;

class SimplePieTable extends Table {

  protected $feed_title;

  /**
   * Sets the title.
   *
   * @param string $title
   *   The feed title.
   */
  public function setFeedTitle($title) {
    $this->feed_title = $title;
  }

  public function get($field) {
    if (isset($this->$field)) {
      return $this->$field;
    }
  }

}
