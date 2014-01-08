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

  const FILE = 'file_1';

  const DIRECTORY = 'directory';

  public function testCheckFile() {
    touch(static::FILE);

    $this->assertFalse(FileSystem::checkFile('does_not_exist'));
    // Remove read permission.
    chmod(static::FILE, 000);
    $this->assertFalse(FileSystem::checkFile(static::FILE));

    chmod(static::FILE, 777);
    $this->assertTrue(FileSystem::checkFile(static::FILE));
  }

  public function testCheckDirectory() {
    mkdir(static::DIRECTORY);

    $this->assertFalse(FileSystem::checkDirectory('does_not_exist'));
    // Remove read permission.
    chmod(static::DIRECTORY, 000);
    $this->assertFalse(FileSystem::checkDirectory(static::DIRECTORY));

    chmod(static::DIRECTORY, 777);
    $this->assertTrue(FileSystem::checkDirectory(static::DIRECTORY));
  }

}
