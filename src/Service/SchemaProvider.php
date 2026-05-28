<?php

declare(strict_types=1);

namespace App\Service;

use Doctrine\DBAL\Schema\Column;
use Doctrine\DBAL\Schema\PrimaryKeyConstraint;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Schema\Table;

final readonly class SchemaProvider
{
    public function createSchema(): Schema
    {
        $users = Table::editor()
            ->setUnquotedName('users')
            ->addColumn(
                Column::editor()
                    ->setUnquotedName('id')
                    ->setTypeName('integer')
                    ->setUnsigned(true)
                    ->setAutoincrement(true)
                    ->create()
            )
            ->addColumn(
                Column::editor()
                    ->setUnquotedName('email')
                    ->setTypeName('string')
                    ->setLength(255)
                    ->create()
            )
            ->addColumn(
                Column::editor()
                    ->setUnquotedName('created_at')
                    ->setTypeName('string')
                    ->setLength(25)
                    ->create()
            )
            ->addPrimaryKeyConstraint(
                PrimaryKeyConstraint::editor()
                    ->setUnquotedColumnNames('id')
                    ->create()
            )
            ->create();

        return new Schema([$users]);
    }
}
