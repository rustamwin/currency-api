<?php

declare(strict_types=1);

use Rustamwin\CurrencyApi\Application;
use Rustamwin\CurrencyApi\Handler\CurrencyApiHandler;

require_once dirname(__DIR__) . '/vendor/autoload.php';

(new Application(new CurrencyApiHandler()))->run();