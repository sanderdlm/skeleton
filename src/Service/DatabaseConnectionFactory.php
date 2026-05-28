<?php

declare(strict_types=1);

namespace App\Service;

use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;

final readonly class DatabaseConnectionFactory
{
    public static function create(string $environment, string $projectRoot): Connection
    {
        $config = new Configuration();

        $params = match ($environment) {
            'test' => [
                'driver' => 'pdo_sqlite',
                'memory' => true,
            ],
            default => [
                'driver' => 'pdo_sqlite',
                'path' => $projectRoot . '/var/data/app.db',
            ],
        };

        $connection = DriverManager::getConnection($params, $config);

        $connection->executeStatement("
            PRAGMA journal_mode = WAL;
            PRAGMA synchronous = NORMAL;
            PRAGMA temp_store = MEMORY;
            PRAGMA busy_timeout = 5000;
            PRAGMA foreign_keys = ON;
        ");

        return $connection;
    }
}
