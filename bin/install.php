#!/usr/bin/env php
<?php

use DI\Container;
use Xiag\Poll\Data\SchemaManager;

/** @var Container $container */
$container = require dirname(__DIR__) . '/app/bootstrap.php';

if (in_array('--down', $argv, true)) {
  echo "Project schema was removed out from the database\n";
  $container->call([SchemaManager::class, 'down']);
} else {
  $container->call([SchemaManager::class, 'up']);
  echo "Project schema was settled up into the database\n";
}
