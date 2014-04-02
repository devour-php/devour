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
    $this->file = new File();
  }

  public function testGetRawPayload() {
    touch(static::FILE);
    $this->assertInstanceOf('GuzzleHttp\Stream\StreamInterface', $this->file->transport(new Source(static::FILE)));

    $this->assertTrue($this->file->runInNewProcess());
  }

  /**
   * @expectedException \RuntimeException
   */
  public function testGetRawPayloadFileNotExists() {
    $this->file->transport(new Source('does_not_exist'));
  }

}
