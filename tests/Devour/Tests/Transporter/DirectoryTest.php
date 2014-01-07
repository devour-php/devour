<?php

namespace Devour\Tests\Transporter;

use Devour\ProgressInterface;
use Devour\Source\Source;
use Devour\Source\SourceInterface;
use Devour\Tests\DevourTestCase;
use Devour\Transporter\Directory;

class DirectoryTest extends DevourTestCase {

  const FILE_1 = './directory_exists/file_1';
  const FILE_2 = './directory_exists/file_2';
  const DIRECTORY = './directory_exists';
  const DIRECTORY_NOT_EXISTS = './directory_not_exists';

  protected $directory;

  public function setUp() {
    mkdir(static::DIRECTORY);
    touch(static::FILE_1);
    touch(static::FILE_2);

    $this->directory = new Directory();
  }

  public function tearDown() {
    unlink(static::FILE_1);
    unlink(static::FILE_2);
    rmdir(static::DIRECTORY);
  }

  /**
   * @expectedException \RuntimeException
   * @expectedExceptionMessage There are no more files left to process.
   */
  public function testGetRawPayload() {
    $source = new Source(static::DIRECTORY);

    // We haven't read any directories yet.
    $this->assertEquals($this->directory->progress(), ProgressInterface::COMPLETE);

    // There are 2 files in the directory.
    foreach (array('file_2', 'file_1') as $key => $file) {
      $payload = $this->directory->transport($source);

      $this->assertInstanceOf('\Devour\Payload\FilePayload', $payload);
      $this->assertEquals($payload->getPath(), static::DIRECTORY . '/' . $file);

      // Check progress.
      $this->assertEquals($this->directory->progress(), ++$key / 2);

    }

    $this->assertEquals($this->directory->progress(), ProgressInterface::COMPLETE);
    // The third call will throw \RuntimeException.
    $this->directory->transport($source);

  }

  /**
   * @expectedException \RuntimeException
   * @expectedExceptionMessage The directory does not exist, or is not readable.
   */
  public function testGetRawPayloadDirectoryNotExists() {
    $source = new Source(static::DIRECTORY_NOT_EXISTS);
    $this->directory->transport($source);
  }

}
