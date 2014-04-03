<?php

/**
 * @file
 * Contains \Devour\Tests\Util\ConfigurationTest.
 */

namespace Devour\Tests\Util;

use Devour\Tests\DevourTestCase;
use Devour\Util\Configuration;

/**
 * @covers \Devour\Util\Configuration
 */
class ConfigurationTest extends DevourTestCase {

  public function testDefaultsApplied() {
    $configuration = [];
    $defaults = ['a' => 1];
    $configuration = Configuration::validate($configuration, $defaults);
    $this->assertSame(1, $configuration['a']);
  }

  /**
   * @expectedException \Devour\Common\Exception\ConfigurationException
   * @expectedExceptionMessage The field "a" is required.
   */
  public function testRequiredField() {
    $configuration = [];
    $required = ['a'];
    $configuration = Configuration::validate($configuration, [], $required);
  }

}
