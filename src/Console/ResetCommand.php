<?php

declare(strict_types=1);

namespace LaravelDoctrine\Migrations\Console;

use Doctrine\DBAL\Connection;
use Exception;
use Illuminate\Console\ConfirmableTrait;
use LaravelDoctrine\Migrations\Configuration\DependencyFactoryProvider;
use function method_exists;

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
        $this->throwExceptionIfPlatformIsNotSupported();

        if (method_exists($this->connection, 'createSchemaManager')) {
            $schemaManager = $this->connection->createSchemaManager();
        } else {
            $schemaManager = $this->connection->getSchemaManager();
        }

        if ($this->connection->getDatabasePlatform()->supportsSequences()) {
            $sequences = $schemaManager->listSequences();
            foreach ($sequences as $s) {
                $schemaManager->dropSequence($s->getQuotedName($this->connection->getDatabasePlatform()));
            }
        }

        $tables = $schemaManager->listTableNames();
        foreach ($tables as $table) {
            $foreigns = $schemaManager->listTableForeignKeys($table);
            foreach ($foreigns as $f) {
                $schemaManager->dropForeignKey($f, $table);
            }
        }

        foreach ($tables as $table) {
            $this->safelyDropTable($table);
        }
    }

    /**
     * @throws Exception
     */
    private function throwExceptionIfPlatformIsNotSupported(): void
    {
        $platformName = $this->connection->getDatabasePlatform()->getName();

        if (!array_key_exists($platformName, $this->getCardinalityCheckInstructions())) {
            throw new Exception(sprintf('The platform %s is not supported', $platformName));
        }
    }

    /**
     * @param string $table
     * @throws \Doctrine\DBAL\Exception
     */
    private function safelyDropTable(string $table): void
    {
        $platformName = $this->connection->getDatabasePlatform()->getName();
        $instructions = $this->getCardinalityCheckInstructions()[$platformName];

        $queryDisablingCardinalityChecks = $instructions['needsTableIsolation'] ?
                                                sprintf($instructions['disable'], $table) :
                                                $instructions['disable'];
        $this->connection->query($queryDisablingCardinalityChecks);

        $schema = $this->connection->getSchemaManager();
        $schema->dropTable($table);

        // When table is already dropped we cannot enable any cardinality checks on it
        // See https://github.com/laravel-doctrine/migrations/issues/50
        if (!$instructions['needsTableIsolation']) {
            $this->connection->query($instructions['enable']);
        }
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    private function getCardinalityCheckInstructions(): array
    {
        return [
            'mssql' => [
                'needsTableIsolation'   => true,
                'disable'               => 'ALTER TABLE %s CHECK CONSTRAINT ALL',
            ],
            'mysql' => [
                'needsTableIsolation'   => false,
                'enable'                => 'SET FOREIGN_KEY_CHECKS = 1',
                'disable'               => 'SET FOREIGN_KEY_CHECKS = 0',
            ],
            'postgresql' => [
                'needsTableIsolation'   => true,
                'disable'               => 'ALTER TABLE %s DISABLE TRIGGER ALL',
            ],
            'sqlite' => [
                'needsTableIsolation'   => false,
                'enable'                => 'PRAGMA foreign_keys = ON',
                'disable'               => 'PRAGMA foreign_keys = OFF',
            ],
        ];
    }
}
