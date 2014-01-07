<?php

namespace Devour\Tests\Payload;

use Devour\Payload\FilePayload;
use Devour\Tests\DevourTestCase;

class FilePayloadTest extends DevourTestCase {

  const FILE_PATH = './tpm_config';

  public function setUp() {
    file_put_contents(static::FILE_PATH, 'boop');
  }

  public function tearDown() {
    unlink(static::FILE_PATH);
  }

  public function testPayloadFactoryFromFile() {
    $payload = new FilePayload(static::FILE_PATH);
    // $this->assertSame(static::FILE_PATH, $payload->getStream());
    $this->assertSame('boop', $payload->getContents());
  }

}
