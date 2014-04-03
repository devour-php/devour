<?php

/**
 * @file
 * Contains \Devour\Importer\Importer.
 */

namespace Devour\Importer;

use Devour\Common\ClearableInterface;
use Devour\Common\ProgressInterface;
use Devour\Common\ValidatorInterface;
use Devour\Parser\ParserInterface;
use Devour\Processor\ProcessorInterface;
use Devour\Source\SourceInterface;
use Devour\Table\HasTableFactoryInterface;
use Devour\Table\TableInterface;
use Devour\Transporter\TransporterInterface;
use GuzzleHttp\Stream\StreamInterface;
use Psr\Log\LoggerAwareTrait;

/**
 * This is a dumb importer that doesn't handle batching, or parallel processing
 * in any intellgent manner.
 */
class Importer implements ImporterInterface {

  use LoggerAwareTrait;

  protected $transporter;

  protected $parser;

  protected $processor;

  protected $group = [];

  protected $parserRequired = TRUE;

  /**
   * {@inheritdoc}
   */
  public function import(SourceInterface $source) {
    do {
      $result = $this->transport($source);

      if ($result instanceof StreamInterface) {
        $this->parse($source, $result);
      }
      elseif ($result instanceof TableInterface) {
        $this->process($source, $result);
      }

      // Uh oh!
      else {
        throw new \DomainException(sprintf('The transporter %s returned an invalid value.', get_class($this->transporter)));
      }

    } while ($this->transporter->progress($source) != ProgressInterface::COMPLETE);
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
  public function parse(SourceInterface $source, StreamInterface $stream) {
    do {
      $parser_result = $this->parser->parse($source, $stream);
      $this->process($source, $parser_result);
    } while ($this->parser instanceof ProgressInterface && $this->parser->progress($source) != ProgressInterface::COMPLETE);
  }

  /**
   * {@inheritdoc}
   */
  public function process(SourceInterface $source, TableInterface $table) {
    do {
      $this->processor->process($source, $table);
    } while ($this->processor instanceof ProgressInterface && $this->processor->progress($source) != ProgressInterface::COMPLETE);
  }

  /**
   * {@inheritdoc}
   */
  public function clear(SourceInterface $source) {
    foreach ($this->group as $part) {
      if ($part instanceof ClearableInterface) {
        $part->clear($source);
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function validate() {
    if (!$this->getTransporter()) {
      throw new \DomainException('The importer does not have a transporter!');
    }
    if ($this->parserRequired && !$this->getParser()) {
      throw new \DomainException('The importer does not have a parser!');
    }
    if (!$this->getProcessor()) {
      throw new \DomainException('The importer does not have a processor!');
    }

    // foreach ($this->group as $part) {
    //   if ($part instanceof ValidatorInterface) {
    //     $part->validate();
    //   }
    // }
  }

  /**
   * {@inheritdoc}
   */
  public function getTransporter() {
    return $this->transporter;
  }

  /**
   * {@inheritdoc}
   */
  public function getParser() {
    return $this->parser;
  }

  /**
   * {@inheritdoc}
   */
  public function getProcessor() {
    return $this->processor;
  }

  /**
   * {@inheritdoc}
   */
  public function setTransporter(TransporterInterface $transporter) {
    $this->transporter = $transporter;
    $this->group['transporter'] = $transporter;

    // This transporter may skip the parsing step.
    $this->parserRequired = !$transporter instanceof HasTableFactoryInterface;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function setParser(ParserInterface $parser) {
    $this->parser = $parser;
    $this->group['parser'] = $parser;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function setProcessor(ProcessorInterface $processor) {
    $this->processor = $processor;
    $this->group['processor'] = $processor;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getLogger() {
    return $this->logger;
  }

}
