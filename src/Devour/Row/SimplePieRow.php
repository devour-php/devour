<?php

/**
 * @file
 * Contains \Devour\Row\SimplePieRow.
 */

namespace Devour\Row;

/**
 * @todo Add all fields.
 */
class SimplePieRow implements RowInterface {

  protected $title;

  protected $id;

  protected $permalink;

  protected $date;

  protected $content;

  protected $author_name;

  /**
   * Sets a value.
   *
   * @param string $field
   *   The field to set.
   * @param mixed $value
   *   The value.
   */
  public function set($field, $value) {
    $this->$field = $value;
  }

  /**
   * Translates a field value.
   *
   * @todo Implement.
   */
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
