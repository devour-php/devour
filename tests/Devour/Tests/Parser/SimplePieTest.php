<?php

/**
 * @file
 * Contains \Devour\Tests\Parser\SimplePieTest.
 */

namespace Devour\Tests\Parser;

use Devour\Parser\SimplePie;
use Devour\Payload\FilePayload;
use Devour\Tests\DevourTestCase;

/**
 * @covers \Devour\Parser\SimplePie
 */
class SimplePieTest extends DevourTestCase {

  public function testParse() {
    $file = dirname(__FILE__) . '/../TestData/drupalplanet.rss2';
    $parser = new SimplePie();
    $result = $parser->parse(new FilePayload($file));

    $first = $result->shift();
    $this->assertEquals('Adaptivethemes: Why I killed Node, may it RIP', $first->get('title'));
    $this->assertEquals(1256317246, $first->get('date'));
    $this->assertEquals('http://adaptivethemes.com/why-i-killed-node-may-it-rip', $first->get('id'));
    $this->assertEquals('http://adaptivethemes.com/why-i-killed-node-may-it-rip', $first->get('permalink'));
    $this->assertEquals('<p>Myself, like many others, have always had an acrimonious relationship with the word &ldquo;node&rdquo;. It didn&rsquo;t exactly get off to a good start when node presented me with a rude &ldquo;wtf&rdquo; moment when we first met. Things only went down hill after that, node remaining aloof and abstract, without ever just coming out and telling me what it actually&nbsp;was.</p>
<div></div>', $first->get('content'));
    $this->assertSame('lawyer@boyer.net (Lawyer Boyer)', $first->get('author_email'));

    $second = $result->shift();
    $this->assertEquals('Midwestern Mac, LLC: Managing News - Revolutionary—not Evolutionary—Step for Drupal', $second->get('title'));
    $this->assertEquals(1256273895, $second->get('date'));
    $this->assertEquals('http://www.midwesternmac.com/blogs/geerlingguy/managing-news-revolutionary%E2%80%94not-evolutionary%E2%80%94step-drupal', $second->get('id'));
    $this->assertEquals('http://www.midwesternmac.com/blogs/geerlingguy/managing-news-revolutionary%E2%80%94not-evolutionary%E2%80%94step-drupal', $second->get('permalink'));
    $this->assertEquals('Fun description', $second->get('content'));
  }

}
