<?php

namespace LaravelDoctrine\Migrations\Console;

use LaravelDoctrine\Migrations\Configuration\DependencyFactoryProvider;

class DumpSchemaCommand extends BaseCommand
{
    protected $signature = 'doctrine:migrations:dump-schema
            {--em= : For a specific EntityManager.}
            {--formatted : Format the generated SQL.}
            {--filter-tables=* : Filter the tables to dump via Regex.}
            {--line-length=120 : Max line length of unformatted lines.}
    ';

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

        $command = new \Doctrine\Migrations\Tools\Console\Command\DumpSchemaCommand($dependencyFactory);

        return $command->run($this->getDoctrineInput($command), $this->output->getOutput());
    }
}
