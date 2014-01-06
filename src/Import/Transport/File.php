<?php

/**
 * @file
 * Contains \Import\Transport\File.
 */

namespace Import\Transport;

use Import\Payload\File as FilePayload;
use Import\Source\SourceInterface;

/**
 * A transport that fetches a payload via a local file.
 */
class File implements TransportInterface {

  /**
   * {@inheritdoc}
   */
  public function getRawPayload(SourceInterface $source) {
    $filepath = $source->getSource();

    if ($this->checkFile($filepath)) {
      return new FilePayload($filepath);
    }

    throw new \RuntimeException();
  }

  protected function checkFile($filepath) {
    return is_file($filepath) && is_readable($filepath);
  }

}
