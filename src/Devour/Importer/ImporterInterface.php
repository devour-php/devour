<?php

/**
 * @file
 * Contains \Devour\Importer\ImporterInterface.
 */

namespace Devour\Importer;

use Devour\Common\ClearableInterface;
use Devour\Common\ValidatorInterface;
use Devour\Parser\ParserInterface;
use Devour\Processor\ProcessorInterface;
use Devour\Source\SourceInterface;
use Devour\Table\TableInterface;
use Devour\Transporter\TransporterInterface;
use Guzzle\Stream\StreamInterface;
use Psr\Log\LoggerAwareInterface;

/**
 * An importer is the manager the controls the import process. It consits of a
 * transporter, parser, and processor. The transporter receives a source, and
 * returns a stream. The parser receives a stream and returns a table. Finally,
 * the processor processes the table.
 */
interface ImporterInterface extends ValidatorInterface, ClearableInterface, LoggerAwareInterface {

  /**
   * Returns the current transporter.
   *
   * @return \Devour\Transporter\TransporterInterface|null
   *   The current transporter, or null if one hasn't been set.
   */
  public function getTransporter();

  /**
   * Returns the current parser.
   *
   * @return \Devour\Parser\ParserInterface|null
   *   The current parser, or null if one hasn't been set.
   */
  public function getParser();

  /**
   * Returns the current processor.
   *
   * @return \Devour\Processor\ProcessorInterface|null
   *   The current processor, or null if one hasn't been set.
   */
  public function getProcessor();

  /**
   * Sets the transporter.
   *
   * @param \Devour\Transporter\TransporterInterface $transporter
   *   The transporter to use for importing.
   *
   * @return self
   *   The importer for chaining.
   */
  public function setTransporter(TransporterInterface $transporter);

  /**
   * Sets the parser.
   *
   * @param \Devour\Parser\ParserInterface $parser
   *   The parser to use for importing.
   *
   * @return self
   *   The importer for chaining.
   */
  public function setParser(ParserInterface $parser);

  /**
   * Sets the processor.
   *
   * @param \Devour\Transporter\TransporterInterface $processor
   *   The processor to use for importing.
   *
   * @return self
   *   The importer for chaining.
   */
  public function setProcessor(ProcessorInterface $processor);

  /**
   * Performs an import.
   *
   * @param \Devour\Source\SourceInterface $source
   *   The source to import from.
   */
  public function import(SourceInterface $source);

  /**
   * Executes the transport stage.
   *
   * @param \Devour\Source\SourceInterface $source
   *   The source to import from.
   */
  public function transport(SourceInterface $source);

  /**
   * Executes the parse stage.
   *
   * @param \Devour\Source\SourceInterface $source
   *   The source to import from.
   */
  public function parse(SourceInterface $source, StreamInterface $table);

  /**
   * Executes the process stage.
   *
   * @param \Devour\Source\SourceInterface $source
   *   The source to import from.
   */
  public function process(SourceInterface $source, TableInterface $table);

}
