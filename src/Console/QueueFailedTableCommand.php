<?php

declare(strict_types=1);

namespace LaravelDoctrine\Migrations\Console;

use LaravelDoctrine\Migrations\Configuration\DependencyFactoryProvider;
use LaravelDoctrine\Migrations\DoctrineCommand\GenerateFailedJobsCommand;

class QueueFailedTableCommand extends BaseCommand
{
    protected $signature = 'doctrine:migrations:queue-failed-table
    {--em= : For a specific EntityManager. }
    {--table=failed_jobs : Name for the table. }';

    protected $description = 'Create a migration for the failed queue jobs database table';

    public function handle(DependencyFactoryProvider $provider): int
    {
        $dependencyFactory = $provider->fromEntityManagerName($this->option('em'));

        $command = new GenerateFailedJobsCommand($dependencyFactory);

        return $command->run($this->getDoctrineInput($command), $this->output->getOutput());
    }
}