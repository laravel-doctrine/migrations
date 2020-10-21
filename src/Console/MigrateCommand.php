<?php

namespace LaravelDoctrine\Migrations\Console;

use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;
use LaravelDoctrine\Migrations\Configuration\Configuration;
use LaravelDoctrine\Migrations\Configuration\ConfigurationProvider;
use LaravelDoctrine\Migrations\Exceptions\ExecutedUnavailableMigrationsException;
use LaravelDoctrine\Migrations\Exceptions\MigrationVersionException;
use LaravelDoctrine\Migrations\Migration;
use LaravelDoctrine\Migrations\Migrator;

class MigrateCommand extends Command
{
    use ConfirmableTrait;

    /**
     * The name and signature of the console command.
     * @var string
     */
    protected $signature = 'doctrine:migrations:migrate
    {version=latest : The version number (YYYYMMDDHHMMSS) or alias (first, prev, next, latest) to migrate to.}
    {--connection= : For a specific connection }
    {--write-sql= : The path to output the migration SQL file instead of executing it. }
    {--dry-run : Execute the migration as a dry run. }
    {--query-time : Time all the queries individually. }
    {--force : Force the operation to run when in production. }
    {--allow-no-migration : Doesn\'t throw an exception if no migration is available. }';

    /**
     * @var string
     */
    protected $description = 'Execute a migration to a specified version or the latest available version.';

    /**
     * Execute the console command.
     *
     * @param ConfigurationProvider $provider
     * @param Migrator              $migrator
     *
     * @throws \Doctrine\DBAL\Migrations\MigrationException
     * @return int
     */
    public function handle(ConfigurationProvider $provider, Migrator $migrator)
    {
        if (!$this->confirmToProceed()) {
            return;
        }

        $configuration = $provider->getForConnection(
            $this->option('connection') ?: null
        );

        try {
            $migration = new Migration(
                $configuration,
                $this->argument('version')
            );
        } catch (MigrationVersionException $e) {
            $this->error($e->getMessage());
        }

        try {
            $migration->checkIfNotExecutedUnavailableMigrations();
        } catch (ExecutedUnavailableMigrationsException $e) {
            $this->handleExecutedUnavailableMigrationsException($e, $configuration);
        }

        if ($path = $this->option('write-sql')) {
            $migrator->migrateToFile($migration, $path);
        } else {
            $migrator->migrate(
                $migration,
                $this->option('dry-run') ? true : false,
                $this->option('query-time') ? true : false,
                $this->option('allow-no-migration') ? true : false
            );
        }

        foreach ($migrator->getNotes() as $note) {
            $this->line($note);
        }
    }

    /**
     * @param ExecutedUnavailableMigrationsException $e
     * @param Configuration                          $configuration
     */
    protected function handleExecutedUnavailableMigrationsException(
        ExecutedUnavailableMigrationsException $e,
        Configuration $configuration
    ) {
        $this->error('WARNING! You have previously executed migrations in the database that are not registered migrations.');

        foreach ($e->getMigrations() as $migration) {
            $this->line(sprintf(
                '    <comment>>></comment> %s (<comment>%s</comment>)',
                $configuration->getDateTime($migration),
                $migration
            ));
        }

        if ($this->input->isInteractive() && !$this->confirm('Are you sure you wish to continue?')) {
            $this->error('Migration cancelled!');
            exit(1);
        }
    }
}
