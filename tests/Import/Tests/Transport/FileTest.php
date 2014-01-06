<?php

namespace Import\Tests\Transport;

use Import\Source\SourceInterface;
use Import\Tests\ImportTestCase;
use Import\Transport\File;

class FileTest extends ImportTestCase {

  const FILE_PATH_EXISTS = './file_exists';
  const FILE_PATH_NOT_EXISTS = './file_does_not_exist';
  const DIRECTORY_EXISTS = './directory_exists';

  protected $file;

  public function setUp() {
    touch(static::FILE_PATH_EXISTS);
    mkdir(static::DIRECTORY_EXISTS);

    $this->file = new File();
  }

  public function tearDown() {
    unlink(static::FILE_PATH_EXISTS);
    rmdir(static::DIRECTORY_EXISTS);
  }

  protected function getMockSource($filepath) {
    $source = $this->getMock('\Import\Source\SourceInterface');

    $source->expects($this->once())
      ->method('getSource')
      ->will($this->returnValue($filepath));

    return $source;
  }

  public function testGetRawPayload() {
    $source = $this->getMockSource(static::FILE_PATH_EXISTS);
    $this->assertInstanceOf('\Import\Payload\File', $this->file->getRawPayload($source));
  }

  /**
   * @expectedException \RuntimeException
   */
  public function testGetRawPayloadFileNotExists() {
    $source = $this->getMockSource(static::FILE_PATH_NOT_EXISTS);
    $this->file->getRawPayload($source);
  }

  /**
   * @expectedException \RuntimeException
   */
  public function testGetRawPayloadDirectory() {
    $source = $this->getMockSource(static::DIRECTORY_EXISTS);
    $this->file->getRawPayload($source);
  }

}
