<?php

namespace Devour\Tests\Processor;

use Devour\Map\NoopMap;
use Devour\Processor\Pdo as PdoProcessor;
use Devour\Row\DynamicRow;
use Devour\Tests\DevourTestCase;

/**
 * @todo Test batching.
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
    $table = $this->getMock('\Devour\Table\TableInterface');
    $map = new NoopMap();

    $rows = array();
    foreach (range(0, 2) as $delta) {
      $row = new DynamicRow($this->pdoData[$delta]);
      $row->setMap($map);
      $row->setTable($table);
      $rows[] = $row;
    }


    $table->expects($this->any())
      ->method('shiftRow')
      ->will($this->onConsecutiveCalls($rows[0], $rows[1], $rows[2]));

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
