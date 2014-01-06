<?php

namespace Devour\Tests\Processor;

use Devour\Processor\Pdo as PdoProcessor;
use Devour\Row\Row;
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
      ->method('getPath')
      ->will($this->returnValue($filepath));

    return $source;
  }

  protected function getMockPayload() {
    $payload = $this->getMock('\Devour\Payload\ParsedPayloadInterface');

    $payload->expects($this->any())
      ->method('shiftRow')
      ->will($this->onConsecutiveCalls(
        new Row($this->pdoData[0]),
        new Row($this->pdoData[1]),
        new Row($this->pdoData[2])
      ));

    return $payload;
  }

  public function testProcess() {
    $payload = $this->getMockPayload();

    $this->pdo->process($payload);

    $result = $this->connection->query("SELECT * FROM my_table");
    $result->setFetchMode(\PDO::FETCH_ASSOC);
    $result = $result->fetchAll();
    foreach ($this->pdoData as $delta => $row) {
      $this->assertEquals($row, $result[$delta]);
    }
  }

}
