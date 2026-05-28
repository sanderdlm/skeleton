<?php

declare(strict_types=1);

namespace App\Service;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\Schema;

final readonly class SchemaManager
{
    public function __construct(
        private SchemaProvider $schemaProvider,
        private Connection $connection,
    ) {
    }

    public function initializeDatabase(): void
    {
        $schema = $this->schemaProvider->createSchema();
        $platform = $this->connection->getDatabasePlatform();

        foreach ($schema->toSql($platform) as $sql) {
            $this->connection->executeStatement($sql);
        }
    }

    public function clearDatabase(): void
    {
        $this->connection->executeStatement('PRAGMA foreign_keys = OFF');

        $schema = $this->schemaProvider->createSchema();

        foreach ($schema->getTables() as $table) {
            $this->connection->executeStatement(
                'DROP TABLE IF EXISTS ' . $table->getObjectName()->toString()
            );
        }

        $this->connection->executeStatement('PRAGMA foreign_keys = ON');
    }
}
