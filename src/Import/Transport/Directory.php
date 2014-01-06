<?php

/**
 * @file
 * Contains \Import\Transport\Directory.
 */

namespace Import\Transport;

use Import\Payload\File as FilePayload;
use Import\ProgressInterface;
use Import\Source\SourceInterface;

/**
 * A transport that fetches a payload via a local directory.
 */
class Directory extends File implements ProgressInterface {

  /**
   * The list of files in the directory.
   *
   * @var array
   */
  protected $files;

  /**
   * The total number of files in the directory.
   *
   * @var int
   */
  protected $totalFileCount;

  /**
   * {@inheritdoc}
   */
  public function getRawPayload(SourceInterface $source) {
    $directory = $source->getSource();

    // Initial pass.
    if ($this->files === NULL) {
      $this->files = $this->listFiles($directory);
      $this->totalFileCount = count($this->files);
    }

    if ($this->files) {
      $file = array_pop($this->files);
      return new FilePayload("$directory/$file");
    }

    throw new \RuntimeException('There are no more files left to process.');
  }

  /**
   * {@inheritdoc}
   */
  public function progress() {
    if ($this->totalFileCount) {
      return ($this->totalFileCount - count($this->files)) / $this->totalFileCount;
    }

    return ProgressInterface::COMPLETE;
  }

  /**
   * Returns the list of files in a given directory.
   *
   * @return array
   *   A list of file paths.
   *
   * @throws \RuntimeException
   *   Thrown if the directory does not exist, or is not readable.
   */
  protected function listFiles($directory) {
    if (!$this->checkDirectory($directory)) {
      throw new \RuntimeException('The directory does not exist, or is not readable.');
    }

    $files = array_diff(scandir($directory), array('.', '..'));

    return array_filter($files, function($file) use($directory) {
      return $this->checkFile("$directory/$file");
    });
  }

  /**
   * Checks that a path is a directory and readable.
   *
   * @param string $directory
   *   A path to a directory.
   *
   * @return bool
   *   True if the path is a directory and readable, false if not.
   */
  protected function checkDirectory($directory) {
    return is_dir($directory) && is_readable($directory);
  }

}
