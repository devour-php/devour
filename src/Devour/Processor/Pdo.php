<?php

/**
 * @file
 * Contains \Devour\Processor\Pdo.
 */

namespace Devour\Processor;

use Devour\ConfigurableInterface;
use Devour\Row\RowInterface;

/**
 * A simple PDO database processor.
 */
class Pdo extends ProcessorBase implements ConfigurableInterface {

  /**
   * The database connection.
   *
   * @var \PDO
   */
  protected $connection;

  /**
   * The database table.
   *
   * @var string
   */
  protected $table;

  /**
   * The columns belonging to this table.
   *
   * @var array
   */
  protected $columns;

  /**
   * Constructs a new Pdo object.
   *
   * @param \PDO $connection
   *   A PDO database connection.
   */
  public function __construct(\PDO $connection, $table) {
    $this->connection = $connection;
    $this->table = $this->escapeTable($table);
    $this->columns = $this->getColumns();

    $this->statement = $this->prepareStatement();
    $this->defaults = array_fill_keys($this->columns, NULL);
  }

  /**
   * {@inheritdoc}
   */
  public static function fromConfiguration(array $configuration) {
    $configuration += array('username' => NULL, 'password' => NULL);
    $connection = new \PDO($configuration['dsn'], $configuration['username'], $configuration['password']);

    return new static($connection, $configuration['table']);
  }

  /**
   * {@inheritdoc}
   */
  protected function processRow(RowInterface $row) {

    $item = array();

    foreach ($row as $field => $value) {
      $item[$this->map($field)] = $value;
    }

    $this->prepare($item);

    $this->save($item);
  }

  protected function map($field) {
    return $field;
  }

  protected function prepare(array &$item) {
    $item += $this->defaults;
  }

  protected function save(array $item) {
    $this->statement->execute($item);
  }

  protected function prepareStatement() {
    $fields = implode(',', $this->columns);

    $placeholders = array();
    foreach ($this->columns as $column) {
      $placeholders[] = ':' . $column;
    }
    $placeholders = implode(',', $placeholders);

    // Prepare our statement.
    return $this->connection->prepare("INSERT INTO {$this->table} ($fields) VALUES ($placeholders)");
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

  protected function getColumns() {
    switch ($this->connection->getAttribute(\PDO::ATTR_DRIVER_NAME)) {
      case 'sqlite':
        return $this->getSqliteColumns();

      default:
        return $this->getMysqlColumns();
        break;
    }
  }

  protected function getMysqlColumns() {
    $result = $this->connection->query('DESCRIBE ' . $this->table);
    $result->setFetchMode(\PDO::FETCH_ASSOC);

    $meta = array();

    foreach ($result as $row) {
      $meta[] = $row['Field'];
    }

    return $meta;
  }

  protected function getSqliteColumns() {
    // Stupid sqlite.
    $result = $this->connection->query("PRAGMA table_info(" . $this->table . ")");
    $result->setFetchMode(\PDO::FETCH_ASSOC);

    $meta = array();

    foreach ($result as $row) {
      $meta[] = $row['name'];
    }

    return $meta;
  }

}
