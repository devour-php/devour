<?php

use Devour\Console\ConsoleRunner;
use Devour\Importer\ImporterFactory;
use Devour\Payload\FilePayload;

require_once __DIR__ . '/../vendor/autoload.php';

$importer = ImporterFactory::fromConfigurationFile($argv[1]);
$payload = new FilePayload($argv[2]);

// This will execute parse and process.
$importer->parse($payload);
