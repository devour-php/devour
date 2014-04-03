<?php

/**
 * @file
 * Contains \Devour\Importer\ImporterBuilder
 */

namespace Devour\Importer;

use Devour\Common\ProgressInterface;
use Devour\Importer\Importer;
use Devour\Map\MapInterface;
use Devour\Parser\ParserInterface;
use Devour\Processor\MappableInterface;
use Devour\Processor\ProcessorInterface;
use Devour\Table\HasTableFactoryInterface;
use Devour\Table\TableFactoryInterface;
use Devour\Transporter\TransporterInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * Provides a fluent API for building importers.
 *
 * Example:
 * @code
 * $importer = ImporterBuilder::get()
 *   ->setProcessLimit(10)
 *   ->setTableFactory($table_factory)
 *   ->setTransporter('Devour\Transporter\Guzzle', $configuration)
 *   ->setParser('Devour\Parser\Csv')
 *   ->setProcessor($processor)
 *   ->build();
 * @endcode
 */
class ImporterBuilder {

  /**
   * The first level of commands to execute.
   *
   * @var int
   */
  const PRIMARY = 30;

  /**
   * The second level of commands to execute.
   *
   * @var int
   */
  const SECONDARY = 20;

  /**
   * The third level of commands to execute.
   *
   * @var int
   */
  const TERTIARY = 10;

  /**
   * The importer being constructed.
   *
   * @var \Devour\Importer\ImporterInterface
   */
  protected $importer;

  /**
   * The commands that should be executed upon build.
   *
   * @var \SplPriorityQueue
   */
  protected $commands;

  /**
   * The objects that are constructed.
   *
   * @var \SplObjectStorage
   */
  protected $clients;

  /**
   * Whether or not the importer requires a parser.
   *
   * @var bool
   */
  protected $parserRequired = TRUE;

  /**
   * Whether the build has been executed.
   *
   * @var bool
   */
  protected $hasBeenExecuted = FALSE;

  /**
   * Constructs an ImporterBuilder object.
   */
  public function __construct() {
    $this->clients = new \SplObjectStorage();
    $this->commands = new \SplPriorityQueue();
  }

  /**
   * Returns a new ImporterBuilder object.
   *
   * @return \Devour\Importer\ImporterBuilder
   *   A new ImporterBuilder.
   */
  public static function get() {
    return new static();
  }

  /**
   * Sets the importer.
   *
   * @param \Devour\Importer\ImporterInterface|string $importer
   *   The importer to use. This can be a class or pre-configured object.
   * @param array $configuration
   *   (optional) If $importer is a class string, and implements
   *   \Devour\Common\ConfigurableInterface, this configuration will be passed
   *   in on creation. Defaults to an empty array.
   *
   * @return $this
   *   The builder to use for chaining.
   */
  public function setImporter($importer, array $configuration = []) {
    $this->importer = $this->buildClient($importer, $configuration);
    return $this;
  }

  /**
   * Sets the transporter.
   *
   * @param \Devour\Transporter\TransporterInterface|string $transporter
   *   The transporter to use. This can be a class or pre-configured object.
   * @param array $configuration
   *   (optional) If $transporter is a class string, and implements
   *   \Devour\Common\ConfigurableInterface, this configuration will be passed
   *   in on creation. Defaults to an empty array.
   *
   * @return $this
   *   The builder to use for chaining.
   */
  public function setTransporter($transporter, array $configuration = []) {
    $transporter = $this->buildClient($transporter, $configuration);
    $this->commands->insert(function() use ($transporter) {
      $this->importer->setTransporter($transporter);
    }, static::PRIMARY);

    return $this;
  }

  /**
   * Sets the parser.
   *
   * @param \Devour\Parser\ParserInterface|string $parser
   *   The parser to use. This can be a class or pre-configured object.
   * @param array $configuration
   *   (optional) If $parser is a class string, and implements
   *   \Devour\Common\ConfigurableInterface, this configuration will be passed
   *   in on creation. Defaults to an empty array.
   *
   * @return $this
   *   The builder to use for chaining.
   */
  public function setParser($parser, array $configuration = []) {
    $parser = $this->buildClient($parser, $configuration);
    $this->commands->insert(function() use ($parser) {
      $this->importer->setParser($parser);
    }, static::PRIMARY);

    return $this;
  }

  /**
   * Sets the processor.
   *
   * @param \Devour\Processor\ProcessorInterface|string $processor
   *   The processor to use. This can be a class or pre-configured object.
   * @param array $configuration
   *   (optional) If $processor is a class string, and implements
   *   \Devour\Common\ConfigurableInterface, this configuration will be passed
   *   in on creation. Defaults to an empty array.
   *
   * @return $this
   *   The builder to use for chaining.
   */
  public function setProcessor($processor, array $configuration = []) {
    $processor = $this->buildClient($processor, $configuration);
    $this->commands->insert(function() use ($processor) {
      $this->importer->setProcessor($processor);
    }, static::PRIMARY);

    return $this;
  }

  /**
   * Sets the process limit.
   *
   * @param int $limit
   *   The number of items to parse during one batch.
   *
   * @return $this
   *   The builder to use for chaining.
   *
   * @see \Devour\Common\ProgressInterface
   */
  public function setProcessLimit($limit) {
    $this->commands->insert(function() use ($limit) {
      foreach ($this->clients as $client) {
        if ($client instanceof ProgressInterface) {
          $client->setProcessLimit($limit);
        }
      }
    }, static::SECONDARY);

    return $this;
  }

  /**
   * Sets the table factory to use.
   *
   * @param \Devour\Table\TableFactoryInterface|string $factory
   *   The table factory to use. This can be a class or configured object.
   * @param array $configuration
   *   (optional) If $factory is a class string, and implements
   *   \Devour\Common\ConfigurableInterface, this configuration will be passed
   *   in on creation. Defaults to an empty array.
   *
   * @return $this
   *   The builder to use for chaining.
   *
   * @see \Devour\Table\HasTableFactoryInterface
   * @see \Devour\Table\TableFactoryInterface
   */
  public function setTableFactory($factory, array $configuration = []) {
    $factory = $this->buildClient($factory, $configuration);

    $command = function() use ($factory) {
      foreach ($this->clients as $client) {
        if ($client instanceof HasTableFactoryInterface) {
          $client->setTableFactory($factory);
        }
      }
    };

    $this->commands->insert($command, static::SECONDARY);

    return $this;
  }

  /**
   * Sets the table class.
   *
   * @param string $table_class
   *   The table class the factory will use.
   *
   * @return $this
   *   The builder to use for chaining.
   *
   * @see \Devour\Table\TableFactoryInterface
   * @see \Devour\Table\TableInterface
   */
  public function setTableClass($table_class) {
    $this->commands->insert(function() use ($table_class) {
      foreach ($this->clients as $client) {
        if ($client instanceof HasTableFactoryInterface) {
          $client->getTableFactory()->setTableClass($table_class);
        }
      }
    }, static::TERTIARY);

    return $this;
  }

  /**
   * Sets the map that will be used with this importer.
   *
   * @param \Devour\Map\MapInterface|string $map
   *   The map class or object.
   * @param array $configuration
   *   (optional) If $map is a class string, and implements
   *   \Devour\Common\ConfigurableInterface, this configuration will be passed
   *   in on creation. Defaults to an empty array.
   *
   * @return $this
   *   The builder to use for chaining.
   *
   * @see \Devour\Table\TableFactoryInterface
   * @see \Devour\Table\TableInterface
   */
  public function setMap($map, array $configuration = []) {
    $map = $this->buildClient($map, $configuration);

    $callback = function() use ($map) {
      $processor = $this->importer->getProcessor();
      if ($processor instanceof MappableInterface) {
        $processor->setMap($map);
      }
    };
    $this->commands->insert($callback, static::TERTIARY);

    return $this;
  }

  /**
   * Sets the logger for clients to use.
   *
   * @param \Psr\Log\LoggerInterface|string $logger
   *   The logger class or object.
   * @param array $configuration
   *   (optional) If $logger is a class string, and implements
   *   \Devour\Common\ConfigurableInterface, this configuration will be passed
   *   in on creation. Defaults to an empty array.
   *
   * @return $this
   *   The builder to use for chaining.
   */
  public function setLogger($logger, array $configuration = []) {
    $logger = $this->buildClient($logger, $configuration);

    $callback = function() use ($logger) {
      call_user_func_array([$this, 'doSetLogger'], [$logger]);
    };
    $this->commands->insert($callback, static::TERTIARY);

    return $this;
  }

  /**
   * Sets a logger on all client objects.
   *
   * @param \Psr\Log\LoggerInterface $logger
   *   The logger to set.
   */
  protected function doSetLogger(LoggerInterface $logger) {
    foreach ($this->clients as $client) {
      if ($client instanceof LoggerAwareInterface) {
        $client->setLogger($logger);
      }
    }
  }

  /**
   * Returns the newly minted importer.
   *
   * This method must be called last, and only once.
   *
   * @return \Devour\Importer\ImporterInterface
   *   The newly configured importer.
   *
   * @throws \LogicException
   *   Thrown if called more than once.
   */
  public function build() {
    if ($this->hasBeenExecuted) {
      throw new \LogicException('Builders can only be used once.');
    }

    $this->hasBeenExecuted = TRUE;

    // Hello old faithful.
    if (!$this->importer) {
      $this->setImporter(new Importer());
    }

    $this->replayCommands();

    if (!$this->importer->getLogger()) {
      $this->doSetLogger(new NullLogger());
    }

    $this->importer->validate();

    // Since this is potentially a long running process, we need to make an
    // effort to clean up after ourselves.
    $importer = $this->importer;
    unset($this->importer, $this->commands, $this->clients);

    return $importer;
  }

  /**
   * Replays the commands that have been stored.
   *
   * Some methods require that an object is already configured before they are
   * called. We store the calls and run them, in order, at the end.
   */
  protected function replayCommands() {
    foreach ($this->commands as $command) {
      $command();
    }
  }

  /**
   * Builds a client class.
   *
   * @param object|string $client_class
   *   A class to configure, or an object to simply pass through.
   * @param array $configuration
   *   If $client_class is a class string, and implements
   *   \Devour\Common\ConfigurableInterface, this configuration will be passed
   *   in on creation.
   *
   * @return object
   *   The newly constructed object.
   *
   * @todo This should be moved somewhere else since it's so useful.
   */
  protected static function buildClass($client_class, array $configuration) {

    if (is_object($client_class)) {
      return $client_class;
    }

    if (!class_exists($client_class)) {
      throw new \RuntimeException(sprintf('The "%s" class does not exist.', $client_class));
    }

    if (is_subclass_of($client_class, 'Devour\Common\ConfigurableInterface')) {
      return $client_class::fromConfiguration($configuration);
    }

    return new $client_class();
  }

  /**
   * Builds a client class and tracks it.
   *
   * We track client objects so that we can replay commands on them later.
   *
   * @param object|string $client_class
   *   A class to configure, or an object to simply pass through.
   * @param array $configuration
   *   If $client_class is a class string, and implements
   *   \Devour\Common\ConfigurableInterface, this configuration will be passed
   *   in on creation.
   *
   * @see \Devour\Importer\ImporterBuilder::buildClass()
   */
  protected function buildClient($client_class, array $configuration) {
    $client = $this->buildClass($client_class, $configuration);
    $this->clients->attach($client);

    return $client;
  }

}
