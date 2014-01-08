<?php

const DEVOUR_COMMAND_START_DIR = __DIR__;

require_once __DIR__ . '/../vendor/autoload.php';

\Devour\Console\ConsoleRunner::getApplication()->run();
