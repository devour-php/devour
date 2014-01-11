<?php

/**
 * @file
 * Contains \Devour\Tests\Transporter\DatabaseTest.
 */

namespace Devour\Tests\Transporter;

use Devour\ProgressInterface;
use Devour\Source\Source;
use Devour\Tests\DevourTestCase;
use Devour\Transporter\Database;

/**
 * @covers \Devour\Transporter\Database
 */
class DatabaseTest extends DevourTestCase {

  public function setUp() {
    $this->connection = new \PDO('sqlite::memory:');

    $create_statement = "CREATE TABLE my_table (
      a varchar(10),
      b varchar(10),
      c varchar(10));";

    $this->connection->exec($create_statement);

    // Test table escape.
    $this->transporter = new Database($this->connection, '~my_table');

    $this->data = array(
      array('a' => 'a1','b' => 'b1','c' => 'c1'),
      array('a' => 'a2','b' => 'b2','c' => 'c2'),
      array('a' => 'a3','b' => 'b3','c' => 'c3'),
    );

    $statement = $this->connection->prepare("INSERT INTO my_table (a,b,c) VALUES (:a,:b,:c)");

    foreach ($this->data as $row) {
      $statement->execute($row);
    }
  }

  public function testTransport() {

    $table = $this->transporter->transport(new Source('my_table'));

    $this->assertSame(count($this->data), count($table));

    foreach ($this->data as $delta => $data_row) {
      $this->assertSame($table[$delta]->getData(), $data_row);
    }
  }

  /**
   * @depends testTransport
   */
  public function testBatchTransport() {
    $this->transporter->setProcessLimit(2);
    $source = new Source('my_table');

    $table = $this->transporter->transport($source);

    $this->assertSame(2, count($table));
    $this->assertSame($this->data[0], $table[0]->getData());
    $this->assertSame($this->data[1], $table[1]->getData());
    $this->assertSame(2/3, $this->transporter->progress($source));

    $table = $this->transporter->transport($source);
    $this->assertSame(1, count($table));
    $this->assertSame($this->data[2], $table[0]->getData());
    $this->assertSame(ProgressInterface::COMPLETE, $this->transporter->progress($source));
  }

  /**
   * @covers \Devour\Transporter\Database::fromConfiguration
   */
  public function testFactory() {
    $transporter = Database::fromConfiguration(array('dsn' => 'sqlite::memory:'));
    $this->assertInstanceof('Devour\Transporter\Database', $transporter);
  }

  /**
   * @expectedException \Devour\Common\Exception\ConfigurationException
   * @expectedExceptionMessage The field "dsn" is required.
   */
  public function testFactoryNoDsn() {
    $transporter = Database::fromConfiguration(array());
  }

}
