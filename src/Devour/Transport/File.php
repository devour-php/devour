<?php

/**
 * @file
 * Contains \Devour\Transport\File.
 */

namespace Devour\Transport;

use Devour\Payload\File as FilePayload;
use Devour\Source\SourceInterface;
use Devour\Util\FileSystem;

/**
 * A transport that fetches a payload via a local file.
 */
class File implements TransportInterface {

  /**
   * {@inheritdoc}
   */
  public function getRawPayload(SourceInterface $source) {
    $filepath = $source->getSource();

    if (FileSystem::checkFile($filepath)) {
      return new FilePayload($filepath);
    }

    throw new \RuntimeException('Nothing more to process.');
  }

}
