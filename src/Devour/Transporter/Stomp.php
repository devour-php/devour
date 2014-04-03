<?php

/**
 * @file
 * Contains \Devour\Transporter\Stomp.
 */

namespace Devour\Transporter;

use Devour\Common\ConfigurableInterface;
use Devour\Common\Exception\ConfigurationException;
use Devour\Source\SourceInterface;
use Devour\Table\HasTableFactoryInterface;
use Devour\Table\HasTableFactoryTrait;
use Devour\Transporter\TransporterInterface;
use FuseSource\Stomp\Stomp as StompConnection;

/**
 * Returns STOMP messages.
 */
class Stomp implements TransporterInterface, HasTableFactoryInterface, ConfigurableInterface {

  use HasTableFactoryTrait;

  /**
   * The stomp connection.
   *
   * @var \FuseSource\Stomp\Stomp
   */
  protected $connection;

  /**
   * The number of rows to return at a time.
   *
   * @var int
   */
  protected $batchSize = 50;

  /**
   * Constructs a Stomp object.
   *
   * @param \FuseSource\Stomp\Stomp $connection
   *   The stomp connection.
   */
  public function __construct(StompConnection $connection) {
    $this->connection = $connection;
  }

  /**
   * {@inheritdoc}
   */
  public function transport(SourceInterface $source) {
    $this->connection->connect();
    $this->connection->subscribe((string) $source);

    $message = $this->connection->readFrame();

    $table = $this->getTableFactory()->create();

    if ($message != NULL) {
      $this->connection->ack($message);
    }

    if (!$message) {
      return $table;
    }

    $row = $table->getNewRow();

    $row->set('command', $message->command);

    if (!empty($message->headers)) {
      foreach ($message->headers as $key => $value) {
        $row->set($key, $value);
      }
    }

    $row->set('body', $message->body);

    return $table;
  }

  /**
   * {@inheritdoc}
   */
  public static function fromConfiguration(array $configuration) {
    foreach (['broker'] as $field) {
      if (empty($configuration[$field])) {
        throw new ConfigurationException(sprintf('The field "%s" is required.', $field));
      }
    }

    $configuration += ['username' => NULL, 'password' => NULL];
    $connection = new StompConnection($configuration['broker']);

    return new static($connection);
  }

  /**
   * {@inheritdoc}
   */
  public function setProcessLimit($limit) {
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function progress(SourceInterface $source) {
    return 0;
  }

  public function __destruct() {
    $this->connection->disconnect();
  }

  /**
   * {@inheritdoc}
   */
  public function runInNewProcess() {
    return TRUE;
  }

}
