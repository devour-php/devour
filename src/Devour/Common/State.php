<?php

/**
 * @file
 * Contains \Devour\Common\State.
 */

namespace Devour\Common;

class State {

  protected $isFirstRun = TRUE;

  public $pointer = 0;

  public function isFirstRun() {
    if ($this->isFirstRun) {
      $this->isFirstRun = FALSE;
      return TRUE;
    }

    return FALSE;
  }
}
