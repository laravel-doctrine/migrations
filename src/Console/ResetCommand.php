<?php

namespace LaravelDoctrine\Migrations\Console;

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
     * Execute the console command.
     *
     * @param ConfigurationProvider $provider
     */
    public function fire(ConfigurationProvider $provider)
    {
        $configuration = $provider->getForConnection(
            $this->option('connection')
        );

        $connection = $configuration->getConnection();
        $schema     = $connection->getSchemaManager();

        $connection->query(sprintf('SET FOREIGN_KEY_CHECKS = 0;'));

        $tables = $schema->listTableNames();
        foreach ($tables as $table) {
            $schema->dropTable($table);
        }
        $connection->query(sprintf('SET FOREIGN_KEY_CHECKS = 1;'));

        $this->info('Database was reset');
    }
}
