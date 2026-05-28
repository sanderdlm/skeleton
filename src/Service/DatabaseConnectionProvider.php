<?php

declare(strict_types=1);

namespace App\Service;

use Doctrine\DBAL\Connection;

final readonly class DatabaseConnectionProvider
{
    public function __construct(
        private string $environment,
        private string $projectRoot,
    ) {
    }

    public function __invoke(): Connection
    {
        $connection = DatabaseConnectionFactory::create($this->environment, $this->projectRoot);

        $dbPath = $this->projectRoot . '/var/data/app.db';
        if ($this->environment !== 'test' && (!file_exists($dbPath) || filesize($dbPath) === 0)) {
            $schemaManager = new SchemaManager(new SchemaProvider(), $connection);
            $schemaManager->initializeDatabase();
        }

        return $connection;
    }
}
