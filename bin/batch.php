<?php

use Devour\Console\ConsoleRunner;
use Devour\Importer\ImporterFactory;
use Devour\Source\Source;
use Guzzle\Stream\Stream;

require_once __DIR__ . '/../vendor/autoload.php';

$importer = ImporterFactory::fromConfigurationFile($argv[1]);
$source = new Source($argv[2]);
$stream = new Stream(fopen($argv[3], 'r+'));

// This will execute parse and process.
$importer->parse($source, $stream);
