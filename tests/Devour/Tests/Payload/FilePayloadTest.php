<?php

/**
 * @file
 * Contains \Devour\Tests\Payload\FilePayloadTest.
 */

namespace Devour\Tests\Payload;

use Devour\Payload\FilePayload;
use Devour\Tests\DevourTestCase;

/**
 * @covers \Devour\Payload\FilePayload
 */
class FilePayloadTest extends DevourTestCase {

  const FILE_PATH = './file_1';

  const FILE_CONTENTS = 'boop';

  public function setUp() {
    $this->cleanUpFiles();
    file_put_contents(static::FILE_PATH, static::FILE_CONTENTS);
  }

  public function tearDown() {
    $this->cleanUpFiles();
  }

  public function testPayloadFactoryFromFile() {
    $payload = new FilePayload(static::FILE_PATH);
    $this->assertSame(static::FILE_PATH, $payload->getPath());
    $this->assertSame(static::FILE_CONTENTS, $payload->getContents());
    $this->assertTrue(is_resource($payload->getStream()));
    $this->assertSame(strlen(static::FILE_CONTENTS), $payload->getSize());

  }

}
