<?php

/**
 * @file
 * Contains \Devour\Transporter\File.
 */

namespace Devour\Transporter;

use Devour\Source\SourceInterface;
use Devour\Util\FileSystem;
use Guzzle\Stream\Stream;

/**
 * A single file transport.
 */
class File implements TransporterInterface {

  /**
   * {@inheritdoc}
   */
  public function transport(SourceInterface $source) {
    $filename = $source->getSource();

    if (FileSystem::checkFile($filename)) {
      return new Stream(fopen($filename, 'r+'));
    }

    throw new \RuntimeException(sprintf('The file "%s" does not exist or is not readable.', $filename));
  }

}
