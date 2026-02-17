<?php

declare(strict_types=1);

namespace LaravelDoctrine\Migrations\Tests;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Platforms\MySQLPlatform;
use Doctrine\DBAL\Schema\AbstractSchemaManager;
use Doctrine\DBAL\Schema\Name\Identifier as NameIdentifier;
use Doctrine\DBAL\Schema\Name\OptionallyQualifiedName;
use Doctrine\Migrations\DependencyFactory;
use LaravelDoctrine\Migrations\Configuration\DependencyFactoryProvider;
use LaravelDoctrine\Migrations\Console\ResetCommand;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;

class ResetCommandTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * @var m\MockInterface|Connection
     */
    protected $connection;

    /**
     * @var m\MockInterface|AbstractSchemaManager
     */
    protected $schemaManager;

    /**
     * @var m\MockInterface|DependencyFactory
     */
    protected $dependencyFactory;

    /**
     * @var m\MockInterface|DependencyFactoryProvider
     */
    protected $provider;

    /**
     * @var ResetCommand
     */
    protected $command;

    protected MySQLPlatform $platform;

    /**
     * @var list<string>
     */
    protected array $executed;

    protected function setUp(): void
    {
        $this->executed = [];

        $this->platform = new class extends MySQLPlatform {
            public function supportsSequences(): bool
            {
                return true;
            }
        };

        $this->connection        = m::mock(Connection::class);
        $this->schemaManager     = m::mock(AbstractSchemaManager::class);
        $this->dependencyFactory = m::mock(DependencyFactory::class);
        $this->provider          = m::mock(DependencyFactoryProvider::class);

        $this->command = new class extends ResetCommand {
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
    }

    public function testItQuotesDbal4NamesWhenResetting(): void
    {
        $sequenceName = OptionallyQualifiedName::unquoted('seq1');
        $tableName    = OptionallyQualifiedName::unquoted('access_codes');
        $foreignName  = NameIdentifier::unquoted('FK_TEST');

        $this->connection->shouldReceive('getDatabasePlatform')
            ->atLeast()->once()
            ->andReturn($this->platform);

        $this->connection->shouldReceive('executeStatement')
            ->times(5)
            ->andReturnUsing(function (string $sql): int {
                $this->executed[] = $sql;
                return 0;
            });

        $this->connection->shouldReceive('createSchemaManager')
            ->twice()
            ->andReturn($this->schemaManager, $this->schemaManager);

        $this->schemaManager->shouldReceive('introspectSequences')
            ->once()
            ->andReturn([
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

        $this->schemaManager->shouldReceive('introspectTableNames')
            ->once()
            ->andReturn([$tableName]);

        $this->schemaManager->shouldReceive('introspectTableForeignKeyConstraints')
            ->with($tableName)
            ->once()
            ->andReturn([
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

        $this->schemaManager->shouldReceive('dropSequence')
            ->once()
            ->withArgs(function (string $sequence): bool {
                self::assertSame('`seq1`', $sequence);
                return true;
            })
            ->andReturnUsing(function (string $sequence): void {
                $this->connection->executeStatement(sprintf('DROP SEQUENCE %s', $sequence));
            });

        $this->schemaManager->shouldReceive('dropForeignKey')
            ->once()
            ->withArgs(function (string $fk, string $table): bool {
                self::assertSame('`FK_TEST`', $fk);
                self::assertSame('`access_codes`', $table);
                return true;
            })
            ->andReturnUsing(function (string $fk, string $table): void {
                $this->connection->executeStatement(sprintf('ALTER TABLE %s DROP FOREIGN KEY %s', $table, $fk));
            });

        $this->schemaManager->shouldReceive('dropTable')
            ->once()
            ->with('`access_codes`')
            ->andReturnUsing(function (string $table): void {
                $this->connection->executeStatement(sprintf('DROP TABLE %s', $table));
            });

        $this->dependencyFactory->shouldReceive('getConnection')
            ->once()
            ->andReturn($this->connection);

        $this->provider->shouldReceive('fromEntityManagerName')
            ->with(null)
            ->once()
            ->andReturn($this->dependencyFactory);

        $this->command->handle($this->provider);

        self::assertSame([
            'DROP SEQUENCE `seq1`',
            'ALTER TABLE `access_codes` DROP FOREIGN KEY `FK_TEST`',
            'SET FOREIGN_KEY_CHECKS = 0',
            'DROP TABLE `access_codes`',
            'SET FOREIGN_KEY_CHECKS = 1',
        ], $this->executed);
    }
}
