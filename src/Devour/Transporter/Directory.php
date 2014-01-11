<?php

/**
 * @file
 * Contains \Devour\Transporter\Directory.
 */

namespace Devour\Transporter;

use Devour\ProgressInterface;
use Devour\Source\SourceInterface;
use Devour\Transporter\TransporterInterface;
use Devour\Util\FileSystem;
use Guzzle\Stream\Stream;

/**
 * A transport that batches over a directory, returning each file individually.
 */
class Directory implements TransporterInterface, ProgressInterface {

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
  public function transport(SourceInterface $source) {
    $directory = $source->getSource();

    // Initial pass.
    if ($this->files === NULL) {
      $this->files = $this->listFiles($directory);
      $this->totalFileCount = count($this->files);
    }

    if ($this->files) {
      $file = array_pop($this->files);
      return new Stream(fopen("$directory/$file", 'r+'));
    }

    throw new \RuntimeException('There are no more files left to process.');
  }

  /**
   * {@inheritdoc}
   */
  public function progress(SourceInterface $source) {
    if ($this->totalFileCount) {
      return (float) ($this->totalFileCount - count($this->files)) / $this->totalFileCount;
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
    if (!FileSystem::checkDirectory($directory)) {
      throw new \RuntimeException('The directory does not exist, or is not readable.');
    }

    $files = array_diff(scandir($directory), array('.', '..'));

    return array_filter($files, function($file) use ($directory) {
      return FileSystem::checkFile("$directory/$file");
    });
  }

  /**
   * {@inheritdoc}
   */
  public function setProcessLimit($limit) {
    // This only processes one file at a time, so this is a no-op.
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function runInNewProcess() {
    return FALSE;
  }

}
