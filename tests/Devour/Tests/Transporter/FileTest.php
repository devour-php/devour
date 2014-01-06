<?php

namespace Devour\Tests\Transporter;

use Devour\Source\SourceInterface;
use Devour\Tests\DevourTestCase;
use Devour\Transporter\File;

class FileTest extends DevourTestCase {

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
    $source = $this->getMock('\Devour\Source\SourceInterface');

    $source->expects($this->once())
      ->method('getSource')
      ->will($this->returnValue($filepath));

    return $source;
  }

  public function testGetRawPayload() {
    $source = $this->getMockSource(static::FILE_PATH_EXISTS);
    $this->assertInstanceOf('\Devour\Payload\FilePayload', $this->file->getRawPayload($source));
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
