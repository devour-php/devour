<?php

/**
 * @file
 * Contains \Devour\Tests\Transporter\DirectoryTest.
 */

namespace Devour\Tests\Transporter;

use Devour\Common\ProgressInterface;
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

  const FILE_3 = 'directory_exists_2/file_3';

  const FILE_4 = 'directory_exists_2/file_4';

  const DIRECTORY_2 = 'directory_exists_2';

  protected $directory;

  public function setUp() {
    mkdir(static::DIRECTORY);
    touch(static::FILE_1);
    touch(static::FILE_2);

    mkdir(static::DIRECTORY_2);
    touch(static::FILE_3);
    touch(static::FILE_4);

    $this->directory = Directory::fromConfiguration(array());
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
    $this->assertEquals($this->directory->progress($source), ProgressInterface::COMPLETE);

    // There are 2 files in the directory.
    $found = array();
    foreach (array('file_1', 'file_2') as $key => $file) {
      $stream = $this->directory->transport($source);
      $found[] = $stream->getMetadata('uri');
      $this->assertInstanceOf('GuzzleHttp\Stream\StreamInterface', $stream);
      // Check progress.
      $this->assertEquals($this->directory->progress($source), ++$key / 2);
    }
    // We can't count on the order for different systems.
    $files = array(realpath(static::DIRECTORY . '/' . 'file_1'), realpath(static::DIRECTORY . '/' . 'file_2'));
    $this->assertEmpty(array_diff($files, $found));

    $this->assertEquals($this->directory->progress($source), ProgressInterface::COMPLETE);
    $this->assertFalse($this->directory->runInNewProcess());

    // Verify that passing in multiple sources to the same transporter works.
    $other_dir = new Source(static::DIRECTORY_2);
    $found = array();
    foreach (array('file_3', 'file_4') as $key => $file) {
      $stream = $this->directory->transport($other_dir);
      $found[] = $stream->getMetadata('uri');
      // Check progress.
      $this->assertEquals($this->directory->progress($other_dir), ++$key / 2);
    }
    // We can't count on the order for different systems.
    $files = array(realpath(static::DIRECTORY_2 . '/' . 'file_3'), realpath(static::DIRECTORY_2 . '/' . 'file_4'));
    $this->assertEmpty(array_diff($files, $found));

    // The third call will throw \RuntimeException.
    $this->directory->transport($source);
  }

  /**
   * @expectedException \InvalidArgumentException
   * @expectedExceptionMessage The "directory_not_exists" directory does not exist.
   */
  public function testGetRawPayloadDirectoryNotExists() {
    $this->directory->transport(new Source('directory_not_exists'));
  }

}
