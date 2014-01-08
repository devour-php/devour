<?php

/**
 * @file
 * Contains \Devour\Importer\Importer.
 */

namespace Devour\Importer;

use Devour\Parser\ParserInterface;
use Devour\Table\TableInterface;
use Devour\Payload\PayloadInterface;
use Devour\Processor\ProcessorInterface;
use Devour\ProgressInterface;
use Devour\Source\SourceInterface;
use Devour\Transporter\TransporterInterface;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

/**
 * This is a dumb importer that doesn't handle batching, or parallel processing
 * in any intellgent manner.
 */
class Importer implements ImporterInterface {

  public $transporter;

  protected $parser;

  protected $processor;

  /**
   * Constructs a new Importer object.
   */
  public function __construct(TransporterInterface $transporter, ParserInterface $parser, ProcessorInterface $processor, array $configuration = array()) {
    $this->transporter = $transporter;
    $this->parser = $parser;
    $this->processor = $processor;
  }

  /**
   * {@inheritdoc}
   */
  public function transport(SourceInterface $source) {
    return $this->transporter->transport($source);
  }

  /**
   * {@inheritdoc}
   */
  public function import(SourceInterface $source) {
    do {
      $payload = $this->transporter->transport($source);
      $this->parse($source, $payload);
    } while ($this->transporter instanceof ProgressInterface && $this->transporter->progress() != ProgressInterface::COMPLETE);
  }

  /**
   * Executes the parsing step.
   */
  public function parse(SourceInterface $source, PayloadInterface $payload) {
    do {
      $parser_result = $this->parser->parse($payload);
      $this->process($source, $parser_result);
    } while ($this->parser instanceof ProgressInterface && $this->parser->progress() != ProgressInterface::COMPLETE);
  }

  /**
   * Executes the processing step.
   */
  protected function process(SourceInterface $source, TableInterface $payload) {
    do {
      $this->processor->process($source, $payload);
    } while ($this->processor instanceof ProgressInterface && $this->processor->progress() != ProgressInterface::COMPLETE);
  }

}
