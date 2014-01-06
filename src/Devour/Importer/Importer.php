<?php

/**
 * @file
 * Contains \Devour\Importer\Importer.
 */

namespace Devour\Importer;

use Devour\Parser\ParserInterface;
use Devour\Payload\ParsedPayloadInterface;
use Devour\Payload\RawPayloadInterface;
use Devour\Processor\ProcessorInterface;
use Devour\ProgressInterface;
use Devour\Source\SourceInterface;
use Devour\Transport\TransportInterface;
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
