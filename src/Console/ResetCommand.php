<?php

declare(strict_types=1);

namespace LaravelDoctrine\Migrations\Console;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception as DBALException;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Platforms\MySQLPlatform;
use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Doctrine\DBAL\Platforms\SQLitePlatform;
use Doctrine\DBAL\Platforms\SQLServerPlatform;
use Illuminate\Console\ConfirmableTrait;
use LaravelDoctrine\Migrations\Configuration\DependencyFactoryProvider;
use RuntimeException;

class ResetCommand extends BaseCommand
{
    use ConfirmableTrait;

    /**
     * The name and signature of the console command.
     * @var string
     */
    protected $signature = 'doctrine:migrations:reset
    {--em= : For a specific EntityManager. }';

    /**
     * @var string
     */
    protected $description = 'Reset all migrations';

    private Connection $connection;

    /** @var array<class-string<AbstractPlatform>, string> */
    private const PLATFORM_MAP = [
        SQLServerPlatform::class => 'mssql',
        MySQLPlatform::class => 'mysql',
        PostgreSQLPlatform::class => 'postgresql',
        SQLitePlatform::class => 'sqlite',
    ];

    /**
     * Execute the console command.
     *
     * @param DependencyFactoryProvider $provider
     */
    public function handle(DependencyFactoryProvider $provider): int
    {
        if (!$this->confirmToProceed()) {
            return 1;
        }

        $dependencyFactory = $provider->fromEntityManagerName(
            $this->option('em')
        );
        $this->connection = $dependencyFactory->getConnection();

        $this->safelyDropTables();

        $this->info('Database was reset');
        return 0;
    }

    private function safelyDropTables(): void
    {
        $platform = $this->getDatabasePlatform();

        $schemaManager = $this->connection->createSchemaManager();

        if ($platform->supportsSequences()) {
            $sequences = $schemaManager->introspectSequences();
            foreach ($sequences as $s) {
                $schemaManager->dropSequence($s->getObjectName()->toString());
            }
        }

        $tables = $schemaManager->introspectTableNames();
        foreach ($tables as $table) {
            $foreigns = $schemaManager->introspectTableForeignKeyConstraints($table);
            foreach ($foreigns as $f) {
                $schemaManager->dropForeignKey($f->getObjectName()->toString(), $table->toString());
            }
        }

        foreach ($tables as $table) {
            $this->safelyDropTable($table->toString());
        }
    }

    /**
     * @param string $table
     * @throws \Doctrine\DBAL\Exception
     */
    private function safelyDropTable(string $table): void
    {
        $platformName = $this->getDatabasePlatformName();
        $instructions = $this->getCardinalityCheckInstructions()[$platformName];

        $queryDisablingCardinalityChecks = $instructions['needsTableIsolation'] ?
            sprintf($instructions['disable'], $table) :
            $instructions['disable'];
        $this->connection->executeStatement($queryDisablingCardinalityChecks);

        $schema = $this->connection->createSchemaManager();
        $schema->dropTable($table);

        // When table is already dropped we cannot enable any cardinality checks on it
        // See https://github.com/laravel-doctrine/migrations/issues/50
        if (!$instructions['needsTableIsolation']) {
            $this->connection->executeStatement($instructions['enable']);
        }
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    private function getCardinalityCheckInstructions(): array
    {
        return [
            'mssql' => [
                'needsTableIsolation' => true,
                'disable' => 'ALTER TABLE %s CHECK CONSTRAINT ALL',
            ],
            'mysql' => [
                'needsTableIsolation' => false,
                'enable' => 'SET FOREIGN_KEY_CHECKS = 1',
                'disable' => 'SET FOREIGN_KEY_CHECKS = 0',
            ],
            'postgresql' => [
                'needsTableIsolation' => true,
                'disable' => 'ALTER TABLE %s DISABLE TRIGGER ALL',
            ],
            'sqlite' => [
                'needsTableIsolation' => false,
                'enable' => 'PRAGMA foreign_keys = ON',
                'disable' => 'PRAGMA foreign_keys = OFF',
            ],
        ];
    }

    /**
     * Returns the database platform name based on the platform map.
     * This is used to for the Cardinality Check Instructions.
     *
     * @throws RuntimeException
     */
    private function getDatabasePlatformName(): string
    {
        $platform = $this->getDatabasePlatform();

        foreach (self::PLATFORM_MAP as $class => $name) {
            if ($platform instanceof $class) {
                return $name;
            }
        }

        // This should never happen because getDatabasePlatform already validates.
        throw new RuntimeException('Unexpected: platform passed validation but no mapping exists.');
    }

    /**
     * Returns the database platform from the connection. 
     * If the platform is not supported or determined an exception will be thrown.
     *
     * @throws RuntimeException
     */
    private function getDatabasePlatform(): AbstractPlatform
    {
        try {
            $platform = $this->connection->getDatabasePlatform();
        } catch (DBALException $e) {
            throw new RuntimeException(
                'Unable to determine database platform: ' . $e->getMessage(),
                previous: $e
            );
        }

        foreach (self::PLATFORM_MAP as $class => $name) {
            if ($platform instanceof $class) {
                return $platform;
            }
        }

        throw new RuntimeException(
            sprintf('The platform %s is not supported', $platform::class)
        );
    }
}
