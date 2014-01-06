<?php

namespace Devour\Tests\Parser;

use Devour\Parser\SimplePie;
use Devour\Tests\DevourTestCase;

class SimplePieTest extends DevourTestCase {

  protected $parser;

  protected $content;

  public function setUp() {
    $this->parser = new SimplePie();
    $this->content = file_get_contents(dirname(__FILE__) . '/../TestData/drupalplanet.rss2');
  }

  public function tearDown() {
  }

  protected function getMockRawPayload($content) {
    $source = $this->getMock('\Devour\Payload\PayloadInterface');

    $source->expects($this->once())
      ->method('getContents')
      ->will($this->returnValue($content));

    return $source;
  }

  public function testParse() {
    $payload = $this->getMockRawPayload($this->content);
    $result = $this->parser->parse($payload);

    $first = $result->shiftRow();
    $this->assertEquals('Adaptivethemes: Why I killed Node, may it RIP', $first->get('title'));
    $this->assertEquals(1256317246, $first->get('date'));
    $this->assertEquals('http://adaptivethemes.com/why-i-killed-node-may-it-rip', $first->get('id'));
    $this->assertEquals('http://adaptivethemes.com/why-i-killed-node-may-it-rip', $first->get('permalink'));
    $this->assertEquals('<p>Myself, like many others, have always had an acrimonious relationship with the word &ldquo;node&rdquo;. It didn&rsquo;t exactly get off to a good start when node presented me with a rude &ldquo;wtf&rdquo; moment when we first met. Things only went down hill after that, node remaining aloof and abstract, without ever just coming out and telling me what it actually&nbsp;was.</p>
<div></div>', $first->get('content'));

    $second = $result->shiftRow();
    $this->assertEquals('Midwestern Mac, LLC: Managing News - Revolutionary—not Evolutionary—Step for Drupal', $second->get('title'));
    $this->assertEquals(1256273895, $second->get('date'));
    $this->assertEquals('http://www.midwesternmac.com/blogs/geerlingguy/managing-news-revolutionary%E2%80%94not-evolutionary%E2%80%94step-drupal', $second->get('id'));
    $this->assertEquals('http://www.midwesternmac.com/blogs/geerlingguy/managing-news-revolutionary%E2%80%94not-evolutionary%E2%80%94step-drupal', $second->get('permalink'));
    $this->assertEquals('Fun description', $second->get('content'));
  }

}
