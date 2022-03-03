<?php

declare(strict_types=1);

use Rustamwin\CurrencyApi\Command\CurrencySyncCommand;
use Rustamwin\CurrencyApi\Command\ServeCommand;
use Symfony\Component\Console\Application;

require_once __DIR__ . '/vendor/autoload.php';

$dotenv = \Dotenv\Dotenv::createUnsafeImmutable(__DIR__);

$dotenv->load();

$app = new Application('Currency API', '1.0.0');

$app->addCommands([new ServeCommand(), new CurrencySyncCommand()]);

$app->run();