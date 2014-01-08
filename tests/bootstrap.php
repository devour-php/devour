<?php

error_reporting(-1);

require_once 'PHPUnit/TextUI/TestRunner.php';
require dirname(__DIR__) . '/vendor/autoload.php';

define('DEVOUR_COMMAND_START_DIR', dirname(__DIR__) . '/bin');

// Turn on Guzzle warnings.
\Guzzle\Common\Version::$emitWarnings = TRUE;
