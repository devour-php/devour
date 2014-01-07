<?php

/**
 * @file
 * Contains \Devour\Parser\SimplePie.
 */

namespace Devour\Parser;

use Devour\Map\NoopMap;
use Devour\Payload\PayloadInterface;

/**
 * Wraps SimplePie to parse RSS/Atom feeds.
 */
class SimplePie extends ParserBase {

  /**
   * {@inheritdoc}
   */
  public function parse(PayloadInterface $payload) {
    $feed = new \SimplePie();

    $table = $this->getTableFactory()->create(new NoopMap());

    // @todo Use file directly.
    $feed->set_raw_data($payload->getContents());
    $feed->init();

    $table->setField('feed_title', $feed->get_title());

    foreach ($feed->get_items(0, 0) as $item) {

      // @todo Add more fields.
      $row = $table->getNewRow();
      $row->set('id', $item->get_id());
      $row->set('permalink', $item->get_permalink());
      $row->set('title', $item->get_title());
      $row->set('date', $item->get_gmdate('U'));
      $row->set('content', $item->get_content());

      if ($author = $item->get_author()) {
        $row->set('author_name', $author->get_name());
        $row->set('author_email', $author->get_email());
      }
    }

    return $table;
  }
}
