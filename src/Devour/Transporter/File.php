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
  public function getRawPayload(SourceInterface $source) {
    $filepath = $source->getSource();

    if (FileSystem::checkFile($filepath)) {
      return new FilePayload($filepath);
    }

    throw new \RuntimeException('Nothing more to process.');
  }

}
