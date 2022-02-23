<?php

declare(strict_types=1);

namespace LaravelDoctrine\Migrations\Console;

use Illuminate\Console\ConfirmableTrait;
use LaravelDoctrine\Migrations\Configuration\DependencyFactoryProvider;

class ExecuteCommand extends BaseCommand
{
    use ConfirmableTrait;

    /**
     * The name and signature of the console command.
     * @var string
     */
    protected $signature = 'doctrine:migrations:execute {versions : The versions to execute }
    {--connection= : For a specific connection.}
    {--write-sql : The path to output the migration SQL file instead of executing it. }
    {--dry-run : Execute the migration as a dry run. }
    {--up : Execute the migration up. }
    {--down : Execute the migration down. }
    {--query-time : Time all the queries individually.}';

    /**
     * @var string
     */
    protected $description = 'Execute a single migration version up or down manually.';

    /**
     * Execute the console command.
     *
     * @param DependencyFactoryProvider $provider
     */
    public function handle(DependencyFactoryProvider $provider): int
    {
        $dependencyFactory = $provider->fromConnectionName($this->option('connection'));

        $command = new \Doctrine\Migrations\Tools\Console\Command\ExecuteCommand($dependencyFactory);

        return $command->run($this->getDoctrineInput($command), $this->output->getOutput());
    }
}
