<?php

namespace LaravelDoctrine\Migrations\Tests;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\AbstractSchemaManager;
use Doctrine\DBAL\Platforms\MySQLPlatform;
use Doctrine\DBAL\Schema\Name\Identifier as NameIdentifier;
use Doctrine\DBAL\Schema\Name\OptionallyQualifiedName;
use Doctrine\Migrations\DependencyFactory;
use LaravelDoctrine\Migrations\Configuration\DependencyFactoryProvider;
use LaravelDoctrine\Migrations\Console\ResetCommand;
use Mockery as m;
use PHPUnit\Framework\TestCase;

class ResetCommandTest extends TestCase
{
    protected function tearDown(): void
    {
        m::close();
    }

    public function testItQuotesDbal4NamesWhenResetting(): void
    {
        $platform = new class extends MySQLPlatform {
            public function supportsSequences(): bool
            {
                return true;
            }
        };

        $sequenceName = OptionallyQualifiedName::unquoted('seq1');
        $tableName    = OptionallyQualifiedName::unquoted('access_codes');
        $foreignName  = NameIdentifier::unquoted('FK_TEST');

        $executed = [];

        $connection = m::mock(Connection::class);
        $connection->shouldReceive('getDatabasePlatform')->andReturn($platform);
        $connection->shouldReceive('executeStatement')->andReturnUsing(function (string $sql) use (&$executed) {
            $executed[] = $sql;
            return 0;
        });

        $schemaManager = m::mock(AbstractSchemaManager::class);
        $schemaManager->shouldReceive('introspectSequences')->andReturn([
            new class ($sequenceName) {
                public function __construct(private OptionallyQualifiedName $name)
                {
                }

                public function getObjectName(): OptionallyQualifiedName
                {
                    return $this->name;
                }
            }
        ]);

        $schemaManager->shouldReceive('introspectTableNames')->andReturn([$tableName]);

        $schemaManager->shouldReceive('introspectTableForeignKeyConstraints')->andReturn([
            new class ($foreignName) {
                public function __construct(private NameIdentifier $name)
                {
                }

                public function getObjectName(): NameIdentifier
                {
                    return $this->name;
                }
            }
        ]);

        $schemaManager->shouldReceive('dropSequence')->andReturnUsing(function (string $sequence) use ($connection) {
            $connection->executeStatement(sprintf('DROP SEQUENCE %s', $sequence));
        });

        $schemaManager->shouldReceive('dropForeignKey')->andReturnUsing(function (string $fk, string $table) use ($connection) {
            $connection->executeStatement(sprintf('ALTER TABLE %s DROP FOREIGN KEY %s', $table, $fk));
        });

        $schemaManager->shouldReceive('dropTable')->andReturnUsing(function (string $table) use ($connection) {
            $connection->executeStatement(sprintf('DROP TABLE %s', $table));
        });
        $connection->shouldReceive('createSchemaManager')->andReturn($schemaManager, $schemaManager);

        $dependencyFactory = m::mock(DependencyFactory::class);
        $dependencyFactory->shouldReceive('getConnection')->andReturn($connection);

        $provider = m::mock(DependencyFactoryProvider::class);
        $provider->shouldReceive('fromEntityManagerName')->with(null)->andReturn($dependencyFactory);

        $command = new class extends ResetCommand {
            public function confirmToProceed($warning = 'Application In Production!', $callback = null): bool
            {
                return true;
            }

            public function option($key = null)
            {
                return null;
            }

            public function info($string, $verbosity = null): void
            {
            }
        };

        $command->handle($provider);

        self::assertSame([
            'DROP SEQUENCE `seq1`',
            'ALTER TABLE `access_codes` DROP FOREIGN KEY `FK_TEST`',
            'SET FOREIGN_KEY_CHECKS = 0',
            'DROP TABLE `access_codes`',
            'SET FOREIGN_KEY_CHECKS = 1',
        ], $executed);
    }
}
