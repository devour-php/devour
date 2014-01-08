<?php

/**
 * @file
 * Contains \Devour\Tests\Util\FileSystemTest.
 */

namespace Devour\Tests\Util;

use Devour\Tests\DevourTestCase;
use Devour\Util\FileSystem;

/**
 * @covers \Devour\Util\FileSystem
 */
class FileSystemTest extends DevourTestCase {

  const FILE = './file_1';

  const DIRECORY = './directory';

  public function setUp() {
    touch(static::FILE);
    mkdir(static::DIRECORY);
  }

  public function tearDown() {
    unlink(static::FILE);
    rmdir(static::DIRECORY);
  }

  public function testCheckFile() {
    $this->assertFalse(FileSystem::checkFile('does_not_exist'));
    // Remove read permission.
    chmod(static::FILE, 000);
    $this->assertFalse(FileSystem::checkFile(static::FILE));

    chmod(static::FILE, 777);
    $this->assertTrue(FileSystem::checkFile(static::FILE));
  }

  public function testCheckDirectory() {
    $this->assertFalse(FileSystem::checkDirectory('does_not_exist'));
    // Remove read permission.
    chmod(static::DIRECORY, 000);
    $this->assertFalse(FileSystem::checkDirectory(static::DIRECORY));

    chmod(static::DIRECORY, 777);
    $this->assertTrue(FileSystem::checkDirectory(static::DIRECORY));
  }

}
