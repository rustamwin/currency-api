<?php

declare(strict_types=1);

namespace Rustamwin\CurrencyApi;

use Cycle\Database\Config\DatabaseConfig;
use Cycle\Database\Config\MySQL\DsnConnectionConfig;
use Cycle\Database\Config\MySQLDriverConfig;
use Cycle\Database\DatabaseManager;
use GuzzleHttp\Psr7\ServerRequest;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Rustamwin\CurrencyApi\Handler\ActionHandlerInterface;

final class DB
{
    private static ?DatabaseManager $dbal = null;

    public static function getDbal(): DatabaseManager
    {
        if (self::$dbal === null) {
            $dbConfig = new DatabaseConfig(
                [
                    'default' => 'default',
                    'databases' => [
                        'default' => ['connection' => 'mysql'],
                    ],
                    'connections' => [
                        'mysql' => new MySQLDriverConfig(
                            new DsnConnectionConfig(
                                getenv('DB_DSN'),
                                getenv('DB_USER'),
                                getenv('DB_PASSWORD')
                            )
                        ),
                    ],
                ]
            );
            self::$dbal = new DatabaseManager($dbConfig);
        }

        return self::$dbal;
    }
}