<?php

/**
 * @file
 * Contains \Devour\Table\SimplePieTable.
 */

namespace Devour\Table;

use Devour\Row\RowInterface;

class SimplePieTable implements TableInterface {

  protected $title;

  protected $rows = array();

  /**
   * Sets the title.
   *
   * @param string $title
   *   The feed title.
   */
  public function setTitle($title) {
    $this->title = $title;
  }

  /**
   * Adds a row.
   */
  public function addRow(RowInterface $row) {
    $this->rows[] = $row;
  }

  /**
   * {@inheritdoc}
   */
  public function shiftRow() {
    return array_shift($this->rows);
  }

}
