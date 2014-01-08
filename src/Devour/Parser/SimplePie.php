<?php

/**
 * @file
 * Contains \Devour\Parser\SimplePie.
 */

namespace Devour\Parser;

use Devour\Source\SourceInterface;
use Devour\Table\TableInterface;
use Guzzle\Stream\StreamInterface;

/**
 * Wraps SimplePie to parse RSS/Atom feeds.
 */
class SimplePie extends ParserBase {

  /**
   * {@inheritdoc}
   */
  public function parse(SourceInterface $source, StreamInterface $stream) {
    $feed = new \SimplePie();

    $table = $this->getTableFactory()->create();

    // @todo Use file directly.
    $feed->set_raw_data((string) $stream);
    $feed->init();

    $table->setField('feed_title', $feed->get_title());

    foreach ($feed->get_items(0, 0) as $item) {
      $this->parseItem($table, $item);
    }

    return $table;
  }

  /**
   * Parses a since feed item.
   *
   * @param \Devour\Table\TableInterface $table
   *   A table obeject.
   * @param \SimplePie_Item $item
   *   A SimplePie item.
   */
  protected function parseItem(TableInterface $table, \SimplePie_Item $item) {
    // @todo Add more fields.
    $row = $table->getNewRow();

    $row->set('id', $item->get_id())
        ->set('permalink', $item->get_permalink())
        ->set('title', $item->get_title())
        ->set('date', $item->get_gmdate('U'))
        ->set('content', $item->get_content());

    if ($author = $item->get_author()) {
      $row->set('author_name', $author->get_name())
          ->set('author_email', $author->get_email());
    }
  }

}
