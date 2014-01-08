<?php

/**
 * @file
 * Contains \Devour\Tests\Transporter\FileTest.
 */

namespace Devour\Tests\Transporter;

use Devour\Source\Source;
use Devour\Tests\DevourTestCase;
use Devour\Transporter\File;

/**
 * @covers \Devour\Transporter\File
 */
class FileTest extends DevourTestCase {

  const FILE = 'file_exists';

  protected $file;

  public function setUp() {
    $this->cleanUpFiles();
    $this->file = new File();
  }

  public function testGetRawPayload() {
    touch(static::FILE);
    $this->assertInstanceOf('\Devour\Payload\FilePayload', $this->file->transport(new Source(static::FILE)));
  }

  /**
   * @expectedException \RuntimeException
   */
  public function testGetRawPayloadFileNotExists() {
    $this->file->transport(new Source('does_not_exist'));
  }

}
