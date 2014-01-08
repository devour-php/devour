<?php

/**
 * @file
 * Contains \Devour\Tests\Processor\PdoTest.
 */

namespace Devour\Tests\Processor;

use Devour\Map\NoopMap;
use Devour\Processor\Pdo as PdoProcessor;
use Devour\Table\Table;
use Devour\Tests\DevourTestCase;

/**
 * @covers \Devour\Processor\Pdo
 */
class PdoTest extends DevourTestCase {

  const DB = './sqlite';

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

    $this->pdoData = array(
      array('a' => 'a1','b' => 'b1','c' => 'c1'),
      array('a' => 'a2','b' => 'b2','c' => 'c2'),
      array('a' => 'a3','b' => 'b3','c' => 'c3'),
    );
  }

  public function tearDown() {}

  protected function getMockPdo() {
    $source = $this->getMock('\PDO');

    $source->expects($this->once())
      ->method('getStream')
      ->will($this->returnValue($filepath));

    return $source;
  }

  protected function getMockTable() {
    $map = new NoopMap();
    $table = new Table($map);

    foreach ($this->pdoData as $data) {
      $table->getNewRow()->setData($data);
    }

    return $table;
  }

  public function testProcess() {
    $payload = $this->getMockTable();

    $this->pdo->process($payload);

    $result = $this->connection->query("SELECT * FROM my_table");
    $result->setFetchMode(\PDO::FETCH_ASSOC);
    $result = $result->fetchAll();
    foreach ($this->pdoData as $delta => $row) {
      $this->assertEquals($row, $result[$delta]);
    }
  }

  public function testProcessUnique() {
    $pdo = new PdoProcessor($this->connection, '~my_table', array('a'));
    $pdo->process($this->getMockTable());

    $result = $this->connection->query("SELECT COUNT(*) FROM my_table")->fetch();
    // Imported 3 rows.
    $this->assertEquals(count($this->pdoData), $result[0]);

    // Import again.
    $pdo->process($this->getMockTable());
    $result = $this->connection->query("SELECT COUNT(*) FROM my_table")->fetch();
    // Still only 3 rows!
    $this->assertEquals(count($this->pdoData), $result[0]);
  }

  public function testProcessUpdate() {
    $pdo = new PdoProcessor($this->connection, '~my_table', array('a'), TRUE);
    $pdo->process($this->getMockTable());

    $result = $this->connection->query("SELECT COUNT(*) FROM my_table")->fetch();
    // Imported 3 rows.
    $this->assertEquals(count($this->pdoData), $result[0]);

    // Change a row.
    $this->pdoData[0]['b'] = 'udpated';
    // Import again.
    $pdo->process($this->getMockTable());
    $result = $this->connection->query("SELECT COUNT(*) FROM my_table")->fetch();
    // Still only 3 rows!
    $this->assertEquals(count($this->pdoData), $result[0]);
  }

  public function testFactory() {
    $processor = PdoProcessor::fromConfiguration(array('dsn' => 'sqlite::memory:', 'table' => 'my_table'));
  }

  /**
   * @expectedException \Devour\Exception\ConfigurationException
   * @expectedExceptionMessage The field "dsn" is required.
   */
  public function testFactoryNoDsn() {
    $processor = PdoProcessor::fromConfiguration(array());
  }

  /**
   * @expectedException \Devour\Exception\ConfigurationException
   * @expectedExceptionMessage The field "table" is required.
   */
  public function testFactoryNoTable() {
    $processor = PdoProcessor::fromConfiguration(array('dsn' => 'sqlite::memory:'));
  }

}
