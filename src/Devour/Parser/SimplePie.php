<?php

/**
 * @file
 * Contains \Devour\Parser\SimplePie.
 */

namespace Devour\Parser;

use Devour\Map\NoopMap;
use Devour\Payload\PayloadInterface;
use Devour\Row\SimplePieRow;
use Devour\Table\SimplePieTable;

/**
 * Wraps SimplePie to parse RSS/Atom feeds.
 */
class SimplePie implements ParserInterface {

  /**
   * {@inheritdoc}
   */
  public function parse(PayloadInterface $payload) {
    $feed = new \SimplePie();

    $table = new SimplePieTable(new NoopMap());

    // @todo Use file directly.
    $feed->set_raw_data($payload->getContents());
    $feed->init();

    $table->setFeedTitle($feed->get_title());

    foreach ($feed->get_items(0, 0) as $item) {

      // @todo Add more fields.
      $row = new SimplePieRow($table);
      $row->set('id', $item->get_id());
      $row->set('permalink', $item->get_permalink());
      $row->set('title', $item->get_title());
      $row->set('date', $item->get_gmdate('U'));
      $row->set('content', $item->get_content());

      if ($author = $item->get_author()) {
        $row->set('author_name', $author->get_name());
        $row->set('author_email', $author->get_email());
      }

      $table->addRow($row);
    }

    return $table;
  }
}
