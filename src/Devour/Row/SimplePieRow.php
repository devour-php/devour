<?php

/**
 * @file
 * Contains \Devour\Row\SimplePieRow.
 */

namespace Devour\Row;

class SimplePieRow implements RowInterface {

  protected $title;

  protected $id;

  protected $permalink;

  protected $date;

  protected $content;

  protected $author_name;

  public function set($field, $value) {
    $this->$field = $value;
  }

  protected function translate($target_field) {
    return $target_field;
  }

  /**
   * {@inheritdoc}
   */
  public function get($target_field) {
    return $this->{$this->translate($target_field)};
  }

}
