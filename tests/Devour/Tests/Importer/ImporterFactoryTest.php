<?php

/**
 * @file
 * Contains \Devour\Tests\Importer\ImporterFactoryTest.
 */

namespace Devour\Tests\Importer;

use Devour\Importer\ImporterFactory;
use Devour\Tests\DevourTestCase;
use Symfony\Component\Yaml\Dumper;

/**
 * @covers \Devour\Importer\ImporterFactory
 */
class ImporterFactoryTest extends DevourTestCase {

  const FILE_PATH = 'tpm_config';

  const EMPTY_FILE = 'empty_file';

  public function setUp() {
    $this->cleanUpFiles();

    $this->configuration = array(
      'importer' => array(
        'class' => 'Devour\Importer\Importer',
        'configuration' => array('thing' => 1),
      ),
      'transporter' => array(
        'class' => 'Devour\Transporter\File',
      ),
      'parser' => array(
        'class' => 'Devour\Parser\Csv',
        'configuration' => array('has_header' => TRUE),
      ),
      'processor' => array(
        'class' => 'Devour\Tests\Processor\StubProcessor',
      ),
    );

    $dumper = new Dumper();
    file_put_contents(static::FILE_PATH, $dumper->dump($this->configuration));
  }

  public function testImporterFactory() {
    $importer = ImporterFactory::fromConfiguration($this->configuration);
    $this->assertSame($this->configuration['importer']['class'], get_class($importer));
  }

  public function testImporterFactoryWithMap() {
    $this->configuration += array(
      'map' => array(
        'configuration' => array(
          '1' => 'a',
          '2' => 'b',
        ),
      ),
    );
    $importer = ImporterFactory::fromConfiguration($this->configuration);
    $this->assertSame($this->configuration['importer']['class'], get_class($importer));

    $this->configuration['map']['class'] = 'Devour\Map\NoopMap';
    $importer = ImporterFactory::fromConfiguration($this->configuration);
    $this->assertSame($this->configuration['importer']['class'], get_class($importer));
  }

  public function testImporterFactoryWithTableClass() {
    $this->configuration['table']['class'] = 'Devour\Table\Table';
    $importer = ImporterFactory::fromConfiguration($this->configuration);
    $this->assertSame($this->configuration['importer']['class'], get_class($importer));
  }

  /**
   * @covers \Devour\Importer\ImporterFactory::fromConfiguration
   *
   * @expectedException \RuntimeException
   * @expectedExceptionMessage The transporter class is required.
   */
  public function testImporterFactoryWithNoPartClass() {
    unset($this->configuration['transporter']['class']);
    ImporterFactory::fromConfiguration($this->configuration);
  }

  /**
   * @covers \Devour\Importer\ImporterFactory::fromConfiguration
   *
   * @expectedException \RuntimeException
   * @expectedExceptionMessage The "IDONTEXISTCLASS" class is unavailable.
   */
  public function testImporterFactoryWithInvalidPartClass() {
    $this->configuration['transporter']['class'] = 'IDONTEXISTCLASS';
    ImporterFactory::fromConfiguration($this->configuration);
  }

  /**
   * @covers \Devour\Importer\ImporterFactory::fromConfigurationFile
   */
  public function testImporterFactoryFromFile() {
    $importer = ImporterFactory::fromConfigurationFile(static::FILE_PATH);
    $this->assertSame($this->configuration['importer']['class'], get_class($importer));
  }

  /**
   * @covers \Devour\Importer\ImporterFactory::fromConfigurationFile
   *
   * @expectedException \RuntimeException
   * @expectedExceptionMessage The configuration file "boop" does not exist or is not readable.
   */
  public function testImporterFactoryFailFromFile() {
    ImporterFactory::fromConfigurationFile('boop');
  }

  /**
   * @covers \Devour\Importer\ImporterFactory::fromConfigurationFile
   *
   * @expectedException \RuntimeException
   * @expectedExceptionMessage The configuration file "empty_file" is invalid.
   */
  public function testImporterFactoryFailFromFileEmpy() {
    touch(static::EMPTY_FILE);
    ImporterFactory::fromConfigurationFile(static::EMPTY_FILE);
  }

}
