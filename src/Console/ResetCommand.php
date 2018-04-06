<?php

namespace LaravelDoctrine\Migrations\Console;

use Doctrine\DBAL\Connection;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;
use LaravelDoctrine\Migrations\Configuration\ConfigurationProvider;

class ResetCommand extends Command
{
    use ConfirmableTrait;

    /**
     * The name and signature of the console command.
     * @var string
     */
    protected $signature = 'doctrine:migrations:reset
    {--connection= : For a specific connection.}';

    /**
     * @var string
     */
    protected $description = 'Reset all migrations';

    /**
     * @var Connection
     */
    private $connection;

    /**
     * Execute the console command.
     *
     * @param ConfigurationProvider $provider
     */
    public function handle(ConfigurationProvider $provider)
    {
        if (!$this->confirmToProceed()) {
            return;
        }
        
        $configuration = $provider->getForConnection(
            $this->option('connection')
        );
        $this->connection = $configuration->getConnection();

        $this->safelyDropTables();

        $this->info('Database was reset');
    }

    private function safelyDropTables()
    {
        $this->throwExceptionIfPlatformIsNotSupported();

        $schema = $this->connection->getSchemaManager();
        $tables = $schema->listTableNames();
        foreach ($tables as $table) {
            $this->safelyDropTable($table);
        }
    }

    /**
     * @throws Exception
     */
    private function throwExceptionIfPlatformIsNotSupported()
    {
        $platformName = $this->connection->getDatabasePlatform()->getName();

        if (!array_key_exists($platformName, $this->getCardinalityCheckInstructions())) {
            throw new Exception(sprintf('The platform %s is not supported', $platformName));
        }
    }

    /**
     * @param string $table
     */
    private function safelyDropTable($table)
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
     * @return array
     */
    private function getCardinalityCheckInstructions()
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
