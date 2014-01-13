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
   * @return self
   *   The builder to use for chaining.
   */
  public function setImporter($importer, array $configuration = array()) {
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
   * @return self
   *   The builder to use for chaining.
   */
  public function setTransporter($transporter, array $configuration = array()) {
    $transporter = $this->buildClient($transporter, $configuration);
    $this->recordCommand(static::PRIMARY, __FUNCTION__, $transporter);

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
   * @return self
   *   The builder to use for chaining.
   */
  public function setParser($parser, array $configuration = array()) {
    $parser = $this->buildClient($parser, $configuration);
    $this->recordCommand(static::PRIMARY, __FUNCTION__, $parser);

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
   * @return self
   *   The builder to use for chaining.
   */
  public function setProcessor($processor, array $configuration = array()) {
    $processor = $this->buildClient($processor, $configuration);
    $this->recordCommand(static::PRIMARY, __FUNCTION__, $processor);

    return $this;
  }

  /**
   * Sets the process limit.
   *
   * @param int $limit
   *   The number of items to parse during one batch.
   *
   * @return self
   *   The builder to use for chaining.
   *
   * @see \Devour\Common\ProgressInterface
   */
  public function setProcessLimit($limit) {
    $this->recordCommand(static::SECONDARY, __FUNCTION__, $limit);

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
   * @return self
   *   The builder to use for chaining.
   *
   * @see \Devour\Table\HasTableFactoryInterface
   * @see \Devour\Table\TableFactoryInterface
   */
  public function setTableFactory($factory, array $configuration = array()) {
    $factory = $this->buildClient($factory, $configuration);
    $this->recordCommand(static::SECONDARY, __FUNCTION__, $factory);

    return $this;
  }

  /**
   * Sets the table class.
   *
   * @param string $table_class
   *   The table class the factory will use.
   *
   * @return self
   *   The builder to use for chaining.
   *
   * @see \Devour\Table\TableFactoryInterface
   * @see \Devour\Table\TableInterface
   */
  public function setTableClass($table_class) {
    $this->recordCommand(static::TERTIARY, __FUNCTION__, $table_class);

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
   * @return self
   *   The builder to use for chaining.
   *
   * @see \Devour\Table\TableFactoryInterface
   * @see \Devour\Table\TableInterface
   */
  public function setMap($map, array $configuration = array()) {
    $map = $this->buildClient($map, $configuration);
    $this->recordCommand(static::TERTIARY, __FUNCTION__, $map);

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
   * @return self
   *   The builder to use for chaining.
   */
  public function setLogger($logger, array $configuration = array()) {
    $logger = $this->buildClient($logger, $configuration);
    $this->recordCommand(static::TERTIARY, __FUNCTION__, $logger);

    return $this;
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
      $method = 'do' . ucfirst($command['method']);
      call_user_func_array(array($this, $method), $command['arguments']);
    }
  }

  /**
   * Sets the transporter.
   *
   * @param \Devour\Transporter\TransporterInterface $transporter
   *   The transporter to set.
   */
  protected function doSetTransporter(TransporterInterface $transporter) {
    $this->importer->setTransporter($transporter);
  }

  /**
   * Sets the parser.
   *
   * @param \Devour\Parser\ParserInterface $parser
   *   The parser to set.
   */
  protected function doSetParser(ParserInterface $parser) {
    $this->importer->setParser($parser);
  }

  /**
   * Sets the processor.
   *
   * @param \Devour\Processor\ProcessorInterface $processor
   *   The processor to set.
   */
  protected function doSetProcessor(ProcessorInterface $processor) {
    $this->importer->setProcessor($processor);
  }

  /**
   * Sets the process limit on objects that support it.
   *
   * @var int $limit
   *   The process limit.
   */
  protected function doSetProcessLimit($limit) {
    foreach ($this->clients as $client) {
      if ($client instanceof ProgressInterface) {
        $client->setProcessLimit($limit);
      }
    }
  }

  /**
   * Sets the table factory on all client objects that support it.
   *
   * @param \Devour\Table\TableFactoryInterface $factory
   *   The table factory.
   */
  protected function doSetTableFactory(TableFactoryInterface $factory) {
    $transporter = $this->importer->getTransporter();
    $parser = $this->importer->getParser();

    if ($transporter instanceof HasTableFactoryInterface) {
      $transporter->setTableFactory($factory);
    }
    if ($parser instanceof HasTableFactoryInterface) {
      $parser->setTableFactory($factory);
    }
  }

  /**
   * Sets the table class on the table factory.
   *
   * @param string $table_class
   *   The table class.
   */
  protected function doSetTableClass($table_class) {
    $transporter = $this->importer->getTransporter();
    $parser = $this->importer->getParser();

    if ($transporter instanceof HasTableFactoryInterface) {
      $transporter->getTableFactory()->setTableClass($table_class);
    }
    if ($parser instanceof HasTableFactoryInterface) {
      $parser->getTableFactory()->setTableClass($table_class);
    }
  }

  /**
   * Sets the map instance on the table factory.
   *
   * @param \Devour\Map\MapInterface $map
   *   The map to use for this importer.
   */
  protected function doSetMap(MapInterface $map) {
    $processor = $this->importer->getProcessor();
    if ($processor instanceof MappableInterface) {
      $processor->setMap($map);
    }
  }

  /**
   * Sets the logger instance on any clients.
   *
   * @param \Psr\Log\LoggerInterface $logger
   *   The logger object.
   */
  protected function doSetLogger(LoggerInterface $logger) {
    foreach ($this->clients as $client) {
      if ($client instanceof LoggerAwareInterface) {
        $client->setLogger($logger);
      }
    }
  }

  /**
   * Records a single command.
   *
   * These will be replayed on build.
   *
   * @param int $priority
   *   The priority of this command. The higher the priority, the sooner it will
   *   be executed.
   * @param string $method
   *   The method name to call when replaying the command.
   * @param mixed $args
   *   The rest of the arguments will be used as arguments to $method.
   */
  protected function recordCommand($priority, $method) {
    $args = func_get_args();
    array_shift($args);
    array_shift($args);
    $this->commands->insert(array('method' => $method, 'arguments' => $args), $priority);
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
