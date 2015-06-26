<?php

/**
 * @file
 * Contains \Devour\Transporter\TransporterInterface.
 */

namespace Devour\Transporter;

use Devour\Common\ProgressInterface;
use Devour\Source\SourceInterface;

/**
 * The interface all transports must implement.
 *
 * A transport is a method of retrieving a stream. Transporters should strive to
 * be payload agnostic, meaning, they shouldn't care about the contents of the
 * stream.
 */
interface TransporterInterface extends ProgressInterface {

  /**
   * Returns a stream, or a table.
   *
   * Most transports should return a stream to be passed to parsers. In some
   * cases, the transport receives that data already parsed, and can create
   * the table on its own. Importers are aware of this and react appropriately,
   * essentially changing the import process into two steps.
   *
   * @param \Devour\Source\SourceInterface $source
   *   A source object.
   *
   * @return \Psr\Http\Message\StreamInterface|\Devour\Table\TableInterface
   *   A stream object or table.
   *
   * @throws \RuntimeException
   *   Thrown if an error occured.
   */
  public function transport(SourceInterface $source);

  /**
   * Returns whether or not this transporter should be spawned in a new process.
   *
   * Some transporters, like the Guzzle transporter, should be spawned in a new
   * process so that they can download in parallel. Others, like the Directory
   * transporter, will return multiple streams that can be handled on their own.
   *
   * @todo There's a parallel here with the usage of ProgressInterface. Should
   *   we tie them together?
   *
   * @return bool
   *   True is this should be run in a new process, false if not.
   */
  public function runInNewProcess();

}
