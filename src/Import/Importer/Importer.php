<?php

/**
 * @file
 * Contains \Import\Importer\Importer.
 */

namespace Import\Importer;

use Import\Parser\ParserInterface;
use Import\Payload\ParsedPayloadInterface;
use Import\Payload\RawPayloadInterface;
use Import\Processor\ProcessorInterface;
use Import\ProgressInterface;
use Import\Source\SourceInterface;
use Import\Transport\TransportInterface;

/**
 * This is a dumb importer that doesn't handle batching, or parallel processing
 * in any intelligent manner.
 */
class Importer implements ImporterInterface {

  protected $transport;

  protected $parser;

  protected $processor;

  public function __construct(TransportInterface $transport, ParserInterface $parser, ProcessorInterface $processor) {
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
    }
    while ($this->transport instanceof ProgressInterface && $this->transport->progress() != ProgressInterface::COMPLETE);
  }

  protected function parse(RawPayloadInterface $payload) {
    do {
      $parser_result = $this->parser->parse($payload);
      $this->process($parser_result);
    }
    while ($this->parser instanceof ProgressInterface && $this->parser->progress() != ProgressInterface::COMPLETE);
  }

  protected function process(ParsedPayloadInterface $payload) {
    do {
      $this->processor->process($payload);
    }
    while ($this->processor instanceof ProgressInterface && $this->processor->progress() != ProgressInterface::COMPLETE);
  }

}
