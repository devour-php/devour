<?php

/**
 * @file
 * Contains \Devour\Importer\Importer.
 */

namespace Devour\Importer;

use Devour\ClearableInterface;
use Devour\Parser\ParserInterface;
use Devour\Processor\ProcessorInterface;
use Devour\ProgressInterface;
use Devour\Source\SourceInterface;
use Devour\Table\TableInterface;
use Devour\Transporter\TransporterInterface;
use Guzzle\Stream\StreamInterface;
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
      $result = $this->transporter->transport($source);

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

    } while ($this->transporter instanceof ProgressInterface && $this->transporter->progress($source) != ProgressInterface::COMPLETE);
  }

  /**
   * Executes the parsing step.
   */
  public function parse(SourceInterface $source, StreamInterface $stream) {
    do {
      $parser_result = $this->parser->parse($source, $stream);
      $this->process($source, $parser_result);
    } while ($this->parser instanceof ProgressInterface && $this->parser->progress($source) != ProgressInterface::COMPLETE);
  }

  /**
   * Executes the processing step.
   */
  protected function process(SourceInterface $source, TableInterface $table) {
    do {
      $this->processor->process($source, $table);
    } while ($this->processor instanceof ProgressInterface && $this->processor->progress($source) != ProgressInterface::COMPLETE);
  }

  /**
   * {@inheritdoc}
   */
  public function clear(SourceInterface $source) {
    foreach (array('transporter', 'parser', 'processor') as $part) {
      if ($this->$part instanceof ClearableInterface) {
        $this->$part->clear($source);
      }
    }
  }

}
