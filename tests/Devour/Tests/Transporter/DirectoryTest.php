<?php

/**
 * @file
 * Contains \Devour\Tests\Transporter\DirectoryTest.
 */

namespace Devour\Tests\Transporter;

use Devour\ProgressInterface;
use Devour\Source\Source;
use Devour\Tests\DevourTestCase;
use Devour\Transporter\Directory;

/**
 * @covers \Devour\Transporter\Directory
 */
class DirectoryTest extends DevourTestCase {

  const FILE_1 = 'directory_exists/file_1';
  const FILE_2 = 'directory_exists/file_2';
  const DIRECTORY = 'directory_exists';

  protected $directory;

  public function setUp() {
    $this->cleanUpFiles();
    mkdir(static::DIRECTORY);
    touch(static::FILE_1);
    touch(static::FILE_2);

    $this->directory = new Directory();
  }

  /**
   * @expectedException \RuntimeException
   * @expectedExceptionMessage There are no more files left to process.
   */
  public function testTransport() {
    // For test coverage sake. This is a no-op.
    $this->directory->setProcessLimit(10);

    $source = new Source(static::DIRECTORY);

    // We haven't read any directories yet.
    $this->assertEquals($this->directory->progress(new Source(NULL)), ProgressInterface::COMPLETE);

    // There are 2 files in the directory.
    foreach (array('file_2', 'file_1') as $key => $file) {
      $stream = $this->directory->transport($source);

      $this->assertInstanceOf('Guzzle\Stream\StreamInterface', $stream);
      $this->assertEquals($stream->getUri(), static::DIRECTORY . '/' . $file);

      // Check progress.
      $this->assertEquals($this->directory->progress(new Source(NULL)), ++$key / 2);
    }

    $this->assertEquals($this->directory->progress(new Source(NULL)), ProgressInterface::COMPLETE);
    // The third call will throw \RuntimeException.
    $this->directory->transport($source);
  }

  /**
   * @expectedException \RuntimeException
   * @expectedExceptionMessage The directory does not exist, or is not readable.
   */
  public function testGetRawPayloadDirectoryNotExists() {
    $this->directory->transport(new Source('directory_not_exists'));
  }

}
