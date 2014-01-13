<?php

/**
 * @file
 * Contains \Devour\Transporter\Database.
 */

namespace Devour\Transporter;

use Devour\Common\ConfigurableInterface;
use Devour\Common\Exception\ConfigurationException;
use Devour\Common\ProgressInterface;
use Devour\Source\SourceInterface;
use Devour\Table\HasTableFactoryInterface;
use Devour\Table\HasTableFactoryTrait;
use Devour\Transporter\TransporterInterface;

/**
 * Returns rows from a database.
 */
class Database implements TransporterInterface, HasTableFactoryInterface, ConfigurableInterface, ProgressInterface {

  use HasTableFactoryTrait;

  /**
   * The database connection.
   *
   * @var \PDO
   */
  protected $connection;

  /**
   * The number of rows to return at a time.
   *
   * @var int
   */
  protected $batchSize = 50;

  /**
   * Constructs a Database object.
   *
   * @param \PDO $connection
   *   A PDO database connection.
   */
  public function __construct(\PDO $connection) {
    $this->connection = $connection;
  }

  /**
   * {@inheritdoc}
   */
  public function transport(SourceInterface $source) {
    $statement = $this->prepareStatement($source);
    $statement->execute();

    $table = $this->getTableFactory()->create();

    while ($row = $statement->fetch(\PDO::FETCH_ASSOC)) {
      $table->getNewRow()->setData($row);
    }

    return $table;
  }

  /**
   * Prepres the select statement.
   *
   * @param \Devour\Source\SourceInterface $source
   *   The source being imported.
   *
   * @return \PDOStatement
   *   The select statement.
   */
  protected function prepareStatement(SourceInterface $source) {
    $table = $this->escapeTable($source);
    $state = $source->getState($this);

    if (!isset($state->total)) {
      $total = $this->connection->query("SELECT COUNT(*) AS total FROM $table")->fetch();
      $state->total = $total['total'];
    }

    // Prepare our statement.
    $statement = $this->connection->prepare("SELECT * FROM $table LIMIT {$this->batchSize} OFFSET {$state->pointer}");
    $state->pointer += $this->batchSize;

    return $statement;
  }

  /**
   * Escapes a table name string.
   *
   * Force all table names to be strictly alphanumeric-plus-underscore.
   *
   * @param string $table
   *   The table name.
   *
   * @return string
   *   The sanitized table name string.
   */
  protected function escapeTable($table) {
    return preg_replace('/[^A-Za-z0-9_.]+/', '', $table);
  }

  /**
   * {@inheritdoc}
   */
  public static function fromConfiguration(array $configuration) {
    foreach (array('dsn') as $field) {
      if (empty($configuration[$field])) {
        throw new ConfigurationException(sprintf('The field "%s" is required.', $field));
      }
    }

    $configuration += array('username' => NULL, 'password' => NULL);
    $connection = new \PDO($configuration['dsn'], $configuration['username'], $configuration['password']);

    return new static($connection);
  }

  /**
   * {@inheritdoc}
   */
  public function setProcessLimit($limit) {
    $this->batchSize = $limit;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function progress(SourceInterface $source) {
    $state = $source->getState($this);

    if (empty($state->total) || $state->pointer >= $state->total) {
      return ProgressInterface::COMPLETE;
    }

    return (float) $state->pointer / $state->total;
  }

  /**
   * {@inheritdoc}
   */
  public function runInNewProcess() {
    return TRUE;
  }

}
