<?php

/**
 * @file
 * Contains \Devour\Row\SimplePieRow.
 */

namespace Devour\Row;

/**
 * @todo Add all fields.
 */
class SimplePieRow extends RowBase {

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
   * {@inheritdoc}
   */
  public function get($target_field) {
    $field = $this->map->getSourceField($target_field);

    if (isset($this->$field)) {
      return $this->$field;
    }

    return $this->table->get($field);
  }

}
