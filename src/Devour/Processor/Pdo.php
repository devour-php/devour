<?php

/**
 * @file
 * Contains \Devour\Processor\Pdo.
 */

namespace Devour\Processor;

use Aura\Sql_Schema\ColumnFactory;
use Devour\ConfigurableInterface;
use Devour\Exception\ConfigurationException;
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
    foreach (array('dsn', 'table') as $field) {
      if (empty($configuration[$field])) {
        throw new ConfigurationException(sprintf('The field "%s" is required.', $field));
      }
    }

    $configuration += array('username' => NULL, 'password' => NULL);
    $connection = new \PDO($configuration['dsn'], $configuration['username'], $configuration['password']);

    return new static($connection, $configuration['table']);
  }

  /**
   * {@inheritdoc}
   */
  protected function processRow(RowInterface $row) {

    $item = array();

    foreach ($this->columns as $field) {
      $item[$field] = $row->get($field);
    }

    $this->prepare($item);

    $this->save($item);
  }

  /**
   * Prepares an item for saving.
   */
  protected function prepare(array &$item) {
    $item += $this->defaults;
  }

  /**
   * Saves an item.
   */
  protected function save(array $item) {
    $this->statement->execute($item);
  }

  /**
   * Builds the prepared statement for inserting new rows.
   */
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

  /**
   * Returns the column names of the table.
   */
  protected function getColumns() {
    $driver = $this->connection->getAttribute(\PDO::ATTR_DRIVER_NAME);

    // Calculate the driver class. Why don't they do this for us?
    $class = '\\Aura\\Sql_Schema\\' . ucfirst($driver) . 'Schema';
    $schema = new $class($this->connection, new ColumnFactory());
    return array_keys($schema->fetchTableCols($this->table));
  }

}
