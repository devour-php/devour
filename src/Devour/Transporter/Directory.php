<?php

/**
 * @file
 * Contains \Devour\Transporter\Directory.
 */

namespace Devour\Transporter;

use Devour\Common\ConfigurableInterface;
use Devour\Common\ProgressInterface;
use Devour\Source\SourceInterface;
use Devour\Transporter\TransporterInterface;
use Guzzle\Stream\Stream;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

/**
 * A transport that batches over a directory, returning each file individually.
 */
class Directory implements TransporterInterface, ConfigurableInterface {

  /**
   * The finder to find files with.
   *
   * @var \Symfony\Component\Finder\Finder
   */
  protected $finder;

  /**
   * Constructs a Directory object.
   *
   * @param \Symfony\Component\Finder\Finder $finder
   *   The configured file iterator to use.
   */
  public function __construct(Finder $finder) {
    $this->finder = $finder;
  }

  /**
   * {@inheritdoc}
   */
  public static function fromConfiguration(array $configuration) {
    // @todo
    $finder = new Finder();
    $finder->files()
           ->ignoreUnreadableDirs();
    return new static($finder);
  }

  /**
   * {@inheritdoc}
   */
  public function transport(SourceInterface $source) {
    $state = $source->getState($this);

    if ($state->isFirstRun()) {
      // We have to clone it, other wise it will remember directories from
      // different sources.
      $finder = clone $this->finder;
      $finder->in((string) $source);

      $state->files = iterator_to_array($finder, FALSE);

      // Get the list of full paths.
      $state->files = array_map(function(\SplFileInfo $file) {
        return $file->getRealpath();
      }, $state->files);

      $state->total = count($state->files);
    }

    if ($state->files) {
      $file = array_pop($state->files);
      return new Stream(fopen($file, 'r'));
    }

    throw new \RuntimeException('There are no more files left to process.');
  }

  /**
   * {@inheritdoc}
   */
  public function progress(SourceInterface $source) {
    $state = $source->getState($this);
    if (!empty($state->total)) {
      return (float) ($state->total - count($state->files)) / $state->total;
    }

    return ProgressInterface::COMPLETE;
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
