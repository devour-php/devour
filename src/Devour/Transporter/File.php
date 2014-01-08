<?php

/**
 * @file
 * Contains \Devour\Transporter\File.
 */

namespace Devour\Transporter;

use Devour\Payload\FilePayload;
use Devour\Source\SourceInterface;
use Devour\Util\FileSystem;

/**
 * A transport that fetches a payload via a local file.
 */
class File implements TransporterInterface {

  /**
   * {@inheritdoc}
   */
  public function transport(SourceInterface $source) {
    $filename = $source->getSource();

    if (FileSystem::checkFile($filename)) {
      return new FilePayload($filename);
    }

    throw new \RuntimeException(sprintf('The file "%s" does not exist or is not readable.', $filename));
  }

}
