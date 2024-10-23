<?php

declare(strict_types=1);

namespace LaravelDoctrine\Migrations\Console;

use LaravelDoctrine\Migrations\Configuration\DependencyFactoryProvider;

class MigrateCommand extends BaseCommand
{
    /**
     * The name and signature of the console command.
     * @var string
     */
    protected $signature = 'doctrine:migrations:migrate
    {version=latest : The version number (YYYYMMDDHHMMSS) or alias (first, prev, next, latest) to migrate to.}
    {--em= : For a specific EntityManager. }
    {--write-sql= : The path to output the migration SQL file instead of executing it. }
    {--dry-run : Execute the migration as a dry run. }
    {--query-time : Time all the queries individually. }
    {--allow-no-migration : Doesn\'t throw an exception if no migration is available. }
    {--all-or-nothing=notprovided : Wrap the entire migration in a transaction. }
    {--no-all-or-nothing : Do not wrap the entire migration in a transaction. }
    ';

    /**
     * @var string
     */
    protected $description = 'Execute a migration to a specified version or the latest available version.';

    public function __construct()
    {
        parent::__construct();

        $this->getDefinition()->getOption('write-sql')->setDefault(false);
    }

    /**
     * Execute the console command.
     *
     * @param DependencyFactoryProvider $provider
     * @return int
     * @throws \Exception
     */
    public function handle(DependencyFactoryProvider $provider): int
    {
        $dependencyFactory = $provider->fromEntityManagerName($this->option('em'));

        $command = new \Doctrine\Migrations\Tools\Console\Command\MigrateCommand($dependencyFactory);

        return $command->run($this->getDoctrineInput($command), $this->output->getOutput());
    }

}
