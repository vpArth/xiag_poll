<?php

use DI\ContainerBuilder;

require_once __DIR__ . '/../vendor/autoload.php';

Dotenv\Dotenv::createImmutable(dirname(__DIR__), ['.env.test.local'])->safeLoad();
Dotenv\Dotenv::createImmutable(dirname(__DIR__), ['.env.test'])->load();
Dotenv\Dotenv::createImmutable(dirname(__DIR__), ['.env.local'])->safeLoad();
Dotenv\Dotenv::createImmutable(dirname(__DIR__))->load();

$containerBuilder = new ContainerBuilder;
$containerBuilder->addDefinitions(__DIR__ . '/config.php');

return $containerBuilder->build();
