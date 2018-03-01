<?php

namespace LaravelDoctrine\Migrations\Console;

use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;
use LaravelDoctrine\Migrations\Configuration\ConfigurationProvider;
use LaravelDoctrine\Migrations\Migrator;

class ExecuteCommand extends Command
{
    use ConfirmableTrait;

    /**
     * The name and signature of the console command.
     * @var string
     */
    protected $signature = 'doctrine:migrations:execute {version : The version to execute }
    {--connection= : For a specific connection.}
    {--write-sql : The path to output the migration SQL file instead of executing it. }
    {--dry-run : Execute the migration as a dry run. }
    {--up : Execute the migration up. }
    {--down : Execute the migration down. }
    {--query-time : Time all the queries individually.}
    {--force : Force the operation to run when in production. }';

    /**
     * @var string
     */
    protected $description = 'Execute a single migration version up or down manually.';

    /**
     * Execute the console command.
     *
     * @param ConfigurationProvider $provider
     * @param Migrator              $migrator
     */
    public function handle(ConfigurationProvider $provider, Migrator $migrator)
    {
        if (!$this->confirmToProceed()) {
            return;
        }

        $configuration = $provider->getForConnection(
            $this->option('connection')
        );

        $version   = $this->argument('version');
        $direction = $this->option('down') ? 'down' : 'up';

        $version = $configuration->getVersion($version);

        if ($path = $this->option('write-sql')) {
            $migrator->executeToFile($version, $direction, $path);
        } else {
            $migrator->execute($version, $direction, $this->option('dry-run'), $this->option('query-time'));
        }

        foreach ($migrator->getNotes() as $note) {
            $this->line($note);
        }
    }
}
