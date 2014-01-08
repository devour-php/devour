<?php

use Devour\Console\ConsoleRunner;
use Devour\Importer\ImporterFactory;
use Devour\Payload\FilePayload;
use Devour\Source\Source;

require_once __DIR__ . '/../vendor/autoload.php';

$importer = ImporterFactory::fromConfigurationFile($argv[1]);
$source = new Source($argv[2]);
$payload = new FilePayload($argv[3]);

// This will execute parse and process.
$importer->parse($source, $payload);
