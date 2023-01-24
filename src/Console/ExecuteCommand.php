<?php

declare(strict_types=1);

namespace LaravelDoctrine\Migrations\Console;

use LaravelDoctrine\Migrations\Configuration\DependencyFactoryProvider;

class ExecuteCommand extends BaseCommand
{
    /**
     * The name and signature of the console command.
     * @var string
     */
    protected $signature = 'doctrine:migrations:execute {versions* : The versions to execute }
    {--em= : For a specific EntityManager. }
    {--write-sql= : The path to output the migration SQL file instead of executing it. }
    {--dry-run : Execute the migration as a dry run. }
    {--up : Execute the migration up. }
    {--down : Execute the migration down. }
    {--query-time : Time all the queries individually.}';

    /**
     * @var string
     */
    protected $description = 'Execute a single migration version up or down manually.';

    public function __construct()
    {
        parent::__construct();

        $this->getDefinition()->getOption('write-sql')->setDefault(false);
    }

    /**
     * Execute the console command.
     *
     * @param DependencyFactoryProvider $provider
     */
    public function handle(DependencyFactoryProvider $provider): int
    {
        $dependencyFactory = $provider->fromEntityManagerName($this->option('em'));

        $command = new \Doctrine\Migrations\Tools\Console\Command\ExecuteCommand($dependencyFactory);

        $this->getDefinition()->getOption('write-sql')->setDefault(false);

        return $command->run($this->getDoctrineInput($command), $this->output->getOutput());
    }
}
