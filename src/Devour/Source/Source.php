<?php

/**
 * @file
 * Contains \Devour\Source\Source.
 */

namespace Devour\Source;

use Devour\Common\State;

/**
 * @todo
 */
class Source implements SourceInterface {

  /**
   * The raw source string.
   *
   * @var string
   */
  protected $source;

  /**
   * Holds the state for phases.
   *
   * @var array
   */
  protected $state;

  /**
   * Constructs a new source object.
   *
   * @param string $source
   *   The raw source string.
   */
  public function __construct($source) {
    $this->source = $source;
  }

  /**
   * {@inheritdoc}
   */
  public function getSource() {
    return $this->source;
  }

  /**
   * {@inheritdoc}
   */
  public function __toString() {
    return $this->getSource();
  }

  /**
   * Tracks state for clients.
   *
   * @param object $client
   *   The object that wants its state tracked.
   *
   * @return \Devour\Common\State
   *   A state object.
   */
  public function getState($client) {
    $class = get_class($client);

    if (!isset($this->state[$class])) {
      $this->state[$class] = new State();
    }

    return $this->state[$class];
  }

}
