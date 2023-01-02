<?php

namespace LaravelDoctrine\Migrations\Console;

use LaravelDoctrine\Migrations\Configuration\DependencyFactoryProvider;

class RollupCommand extends BaseCommand
{
    protected $signature = "doctrine:migrations:rollup
        {--em= : For a specific EntityManager. }
    ";

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

        $command = new \Doctrine\Migrations\Tools\Console\Command\RollupCommand($dependencyFactory);

        return $command->run($this->getDoctrineInput($command), $this->output->getOutput());
    }
}
