<?php

use Devour\Console\ConsoleRunner;
use Devour\Importer\ImporterFactory;
use Devour\Source\Source;
use Guzzle\Stream\Stream;

require_once __DIR__ . '/../vendor/autoload.php';

$action = $argv[1];
$importer = ImporterFactory::fromConfigurationFile($argv[2]);
$source = new Source($argv[3]);


if ($action === 'transport') {
  $importer->import($source);
}
elseif ($action === 'parse') {
  $stream = new Stream(fopen($argv[4], 'r+'));
  // This will parse and process.
  $importer->parse($source, $stream);
}

exit(0);
