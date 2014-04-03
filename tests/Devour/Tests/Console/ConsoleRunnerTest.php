<?php

/**
 * @file
 * Contains \Devour\Tests\Console\ConsoleRunnerTest.
 */

namespace Devour\Tests\Console;

use Devour\Console\ConsoleRunner;
use Devour\Importer\Importer;
use Devour\Tests\DevourTestCase;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\StreamOutput;
use Symfony\Component\Yaml\Dumper;

/**
 * @covers \Devour\Console\ConsoleRunner
 */
class ConsoleRunnerTest extends DevourTestCase {

  const FILE = 'importer.yml';

  const FILE_2 = 'devour.yml';

  protected $input;

  protected $output;

  protected $app;

  public function setUp() {
    $this->input = new ArrayInput([]);
    $this->output = new StreamOutput(fopen('php://memory', 'w', false));
    ConsoleRunner::runApplication($this->input, $this->output, FALSE);
    $this->app = ConsoleRunner::getApplication();
  }

  public function testGetApp() {
    $this->assertInstanceOf('Devour\Console\ConsoleRunner', $this->app);
  }

  public function testGetImporter() {
    $importer = new Importer();
    $this->assertNull($this->app->getImporter());
    $this->app->setImporter($importer);
    $this->assertSame($importer, $this->app->getImporter());
  }

  public function testProfile() {
    $input = new ArrayInput(['--profile' => TRUE]);
    $this->app->run($input, $this->output);
    rewind($this->output->getStream());
    $display = stream_get_contents($this->output->getStream());
    $this->assertRegExp('/Memory usage: .*MB \(peak: .*MB\), time: .*s/', $display);
  }

  public function testFromConfig() {
    $configuration = [
      'importer' => [
        'class' => 'Devour\Importer\Importer',
      ],
      'transporter' => [
        'class' => 'Devour\Transporter\File',
      ],
      'parser' => [
        'class' => 'Devour\Parser\Csv',
      ],
      'processor' => [
        'class' => 'Devour\Tests\Processor\ProcessorStub',
      ],
    ];

    $dumper = new Dumper();
    file_put_contents(static::FILE, $dumper->dump($configuration));
    $input = new ArrayInput(['--config' => static::FILE]);
    $this->app->run($input, $this->output);

    file_put_contents(static::FILE_2, $dumper->dump($configuration));
    $input = new ArrayInput([]);
    $this->app->run($input, $this->output);

    $this->assertSame(static::FILE_2, $this->app->getImporterConfigurationFile());
    $importer = $this->app->getImporter();
    $this->assertInstanceOf('Devour\Transporter\File', $importer->getTransporter());

    // Throws exception.
    $input = new ArrayInput(['--config' => 'crappy.yml']);
    $this->app->run($input, $this->output);
    rewind($this->output->getStream());
    $display = stream_get_contents($this->output->getStream());
    $this->assertRegExp('/\[RuntimeException\]/', $display);
    $this->assertRegExp('/Unable to read "crappy\.yml"\./', $display);
  }

  public function testBootstrap() {
    $input = new ArrayInput(['--bootstrap' => 'boop.php']);
    $this->app->run($input, $this->output);
    // Check for exception.
    rewind($this->output->getStream());
    $display = stream_get_contents($this->output->getStream());
    $this->assertRegExp('/\[RuntimeException\]/', $display);
    $this->assertRegExp('/Invalid bootstrap file\./', $display);

    // Test valid bootstrap.
    file_put_contents(static::FILE, "<?php\n");
    $input = new ArrayInput(['--bootstrap' => static::FILE]);
    $this->app->run($input, $this->output);
  }

}
