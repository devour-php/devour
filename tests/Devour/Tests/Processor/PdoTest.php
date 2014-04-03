<?php

/**
 * @file
 * Contains \Devour\Tests\Processor\PdoTest.
 */

namespace Devour\Tests\Processor;

use Devour\Map\Map;
use Devour\Processor\Pdo as PdoProcessor;
use Devour\Source\Source;
use Devour\Table\Table;
use Devour\Tests\DevourTestCase;

/**
 * @covers \Devour\Processor\Pdo
 */
class PdoTest extends DevourTestCase {

  protected $pdo;

  protected $pdoData;

  protected $connection;

  public function setUp() {
    $this->connection = new \PDO('sqlite::memory:');

    $create_statement = "CREATE TABLE my_table (
      a varchar(10),
      b varchar(10),
      c varchar(10));";

    $this->connection->exec($create_statement);

    // Test table escape.
    $this->pdo = new PdoProcessor($this->connection, '~my_table');

    $this->pdoData = [
      ['a' => 'a1','b' => 'b1','c' => 'c1'],
      ['a' => 'a2','b' => 'b2','c' => 'c2'],
      ['a' => 'a3','b' => 'b3','c' => 'c3'],
    ];

    $this->map = [
      ['a', 'a'],
      ['b', 'b'],
      ['c', 'c'],
    ];

    $this->map = new Map($this->map);
  }

  public function testProcess() {
    $source = new Source(NULL);

    $table = $this->getStubTable($this->pdoData);
    $this->pdo->setMap($this->map);

    $this->assertSame($this->map, $this->pdo->getMap());

    $this->pdo->process($source, $table);

    $result = $this->connection->query("SELECT * FROM my_table");
    $result->setFetchMode(\PDO::FETCH_ASSOC);
    $result = $result->fetchAll();
    foreach ($this->pdoData as $delta => $row) {
      $this->assertEquals($row, $result[$delta]);
    }
  }

  public function testProcessUnique() {
    $source = new Source(NULL);

    $pdo = new PdoProcessor($this->connection, '~my_table', ['a']);
    $pdo->setMap($this->map);
    $pdo->process($source, $this->getStubTable($this->pdoData));

    $result = $this->connection->query("SELECT COUNT(*) FROM my_table")->fetch();
    // Imported 3 rows.
    $this->assertEquals(count($this->pdoData), $result[0]);

    // Import again.
    $pdo->process($source, $this->getStubTable($this->pdoData));
    $result = $this->connection->query("SELECT COUNT(*) FROM my_table")->fetch();
    // Still only 3 rows!
    $this->assertEquals(count($this->pdoData), $result[0]);
  }

  public function testProcessUpdate() {
    $source = new Source(NULL);

    $pdo = new PdoProcessor($this->connection, '~my_table', ['a'], TRUE);
    $pdo->setMap($this->map);
    $pdo->process($source, $this->getStubTable($this->pdoData));

    $result = $this->connection->query("SELECT COUNT(*) FROM my_table")->fetch();
    // Imported 3 rows.
    $this->assertEquals(count($this->pdoData), $result[0]);

    // Change a row.
    $this->pdoData[0]['b'] = 'udpated';
    // Import again.
    $pdo->process($source, $this->getStubTable($this->pdoData));
    $result = $this->connection->query("SELECT COUNT(*) FROM my_table")->fetch();
    // Still only 3 rows!
    $this->assertEquals(count($this->pdoData), $result[0]);
  }

  /**
   * @covers \Devour\Processor\Pdo::fromConfiguration
   */
  public function testFactory() {
    $processor = PdoProcessor::fromConfiguration(['dsn' => 'sqlite::memory:', 'table' => 'my_table']);
  }

  /**
   * @expectedException \Devour\Common\Exception\ConfigurationException
   * @expectedExceptionMessage The field "dsn" is required.
   */
  public function testFactoryNoDsn() {
    $processor = PdoProcessor::fromConfiguration([]);
  }

  /**
   * @expectedException \Devour\Common\Exception\ConfigurationException
   * @expectedExceptionMessage The field "table" is required.
   */
  public function testFactoryNoTable() {
    $processor = PdoProcessor::fromConfiguration(['dsn' => 'sqlite::memory:']);
  }

}
