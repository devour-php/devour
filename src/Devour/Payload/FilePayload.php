<?php

/**
 * @file
 * Contains \Devour\Payload\FilePayload.
 */

namespace Devour\Payload;

/**
 * A regular, saved-to-disk, file.
 */
class FilePayload implements PayloadInterface {

  /**
   * The name of the file.
   *
   * @var string
   */
  protected $filename;

  /**
   * Constructs a File object.
   *
   * @param string $filename
   *   The name of the file.
   */
  public function __construct($filename) {
    $this->filename = $filename;
  }

  /**
   * {@inheritdoc}
   */
  public function getSize() {
    return filesize($this->filename);
  }

  /**
   * {@inheritdoc}
   */
  public function getStream() {
    return fopen($this->filename, 'r+');
  }

  /**
   * {@inheritdoc}
   */
  public function getContents() {
    return file_get_contents($this->filename);
  }

}
