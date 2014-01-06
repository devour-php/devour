<?php

/**
 * @file
 * Contains \Import\Transport\File.
 */

namespace Import\Transport;

use Import\Payload\File as FilePayload;
use Import\Source\SourceInterface;
use Import\Util\FileSystem;

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
