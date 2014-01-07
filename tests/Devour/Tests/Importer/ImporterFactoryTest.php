<?php

namespace Devour\Tests\Importer;

use Devour\Importer\ImporterFactory;
use Devour\Tests\DevourTestCase;
use Symfony\Component\Yaml\Dumper;

class ImporterFactoryTest extends DevourTestCase {

  const FILE_PATH = './tpm_config';

  public function setUp() {

    $this->configuration = array(
      'importer' => array(
        'class' => 'Devour\Importer\Importer',
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

  public function tearDown() {
    unlink(static::FILE_PATH);
  }

  public function testImporterFactory() {
    $importer = ImporterFactory::fromConfiguration($this->configuration);
    $this->assertEquals($this->configuration['importer']['class'], get_class($importer));
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
    $this->assertEquals($this->configuration['importer']['class'], get_class($importer));

    $this->configuration['map']['class'] = 'Devour\Map\NoopMap';
    $importer = ImporterFactory::fromConfiguration($this->configuration);
    $this->assertEquals($this->configuration['importer']['class'], get_class($importer));
  }

  public function testImporterFactoryWithTableClass() {
    $this->configuration['table']['class'] = 'Devour\Table\Table';
    $importer = ImporterFactory::fromConfiguration($this->configuration);
    $this->assertEquals($this->configuration['importer']['class'], get_class($importer));
  }

  public function testImporterFactoryFromFile() {
    $importer = ImporterFactory::fromConfigurationFile(static::FILE_PATH);
    $this->assertEquals(trim($this->configuration['importer']['class'], '\\'), get_class($importer));
  }

  /**
   * @expectedException \LogicException
   * @expectedExceptionMessage The configuration file "boop" does not exist or is not readable.
   */
  public function testImporterFactoryFailFromFile() {
    ImporterFactory::fromConfigurationFile('boop');
  }

}
