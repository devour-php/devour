<?php

/**
 * @file
 * Contains \Devour\Parser\SimplePie.
 */

namespace Devour\Parser;

use Devour\Payload\RawPayloadInterface;
use Devour\Payload\SimplePie as SimplePiePayload;

/**
 * Wraps SimplePie to parse RSS/Atom feeds.
 */
class SimplePie implements ParserInterface {

  /**
   * {@inheritdoc}
   */
  public function parse(RawPayloadInterface $payload) {
    $feed = new \SimplePie();

    $result = new SimplePiePayload();

    // @todo Use file directly.
    $feed->set_raw_data($payload->getContents());
    $feed->init();

    $result->setTitle($feed->get_title());

    foreach ($feed->get_items(0, 0) as $item) {

      // @todo Add more fields.
      $row = array(
        'guid' => $item->get_id(),
        'permalink' => $item->get_permalink(),
        'title' => $item->get_title(),
        'date' => $item->get_gmdate('U'),
        'content' => $item->get_content(),
      );

      if ($author = $item->get_author()) {
        $row['author_name'] = $author->get_name();
      }

      $result->addRow($row);
    }

    return $result;
  }
}
