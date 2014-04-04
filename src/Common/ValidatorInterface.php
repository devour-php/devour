<?php

/**
 * @file
 * Contains \Devour\Common\ValidatorInterface.
 */

namespace Devour\Common;

/**
 * Validates an object, ensuring that its state is correct.
 */
interface ValidatorInterface {

  /**
   * Validates the object.
   *
   * @throws \DomainException
   *   Thrown when something is wrong with the object's state.
   *
   * @return void
   */
  public function validate();

}
