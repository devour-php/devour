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

  protected $transport;

  protected $parser;

  protected $processor;

  /**
   * Constructs a new Importer object.
   */
  public function __construct(TransporterInterface $transport, ParserInterface $parser, ProcessorInterface $processor) {
    $this->transport = $transport;
    $this->parser = $parser;
    $this->processor = $processor;
  }

  /**
   * {@inheritdoc}
   */
  public function import(SourceInterface $source) {
    do {
      $payload = $this->transport->getRawPayload($source);
      $this->parse($payload);
    } while ($this->transport instanceof ProgressInterface && $this->transport->progress() != ProgressInterface::COMPLETE);
  }

  /**
   * Executes the parsing step.
   */
  protected function parse(PayloadInterface $payload) {
    do {
      $parser_result = $this->parser->parse($payload);
      $this->process($parser_result);
    } while ($this->parser instanceof ProgressInterface && $this->parser->progress() != ProgressInterface::COMPLETE);
  }

  /**
   * Executes the processing step.
   */
  protected function process(TableInterface $payload) {
    do {
      $this->processor->process($payload);
    } while ($this->processor instanceof ProgressInterface && $this->processor->progress() != ProgressInterface::COMPLETE);
  }

}
