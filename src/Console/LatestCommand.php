<?php

declare(strict_types=1);

namespace LaravelDoctrine\Migrations\Console;

use LaravelDoctrine\Migrations\Configuration\DependencyFactoryProvider;
use Symfony\Component\Console\Input\ArrayInput;

class LatestCommand extends BaseCommand
{
    /**
     * The name and signature of the console command.
     * @var string
     */
    protected $signature = 'doctrine:migrations:latest
    {--connection= : For a specific connection.}';

    /**
     * @var string
     */
    protected $description = 'Outputs the latest version number';

    /**
     * Execute the console command.
     *
     * @param DependencyFactoryProvider $provider
     */
    public function handle(DependencyFactoryProvider $provider): int
    {
        $dependencyFactory = $provider->fromConnectionName($this->option('connection'));

        $command = new \Doctrine\Migrations\Tools\Console\Command\LatestCommand($dependencyFactory);

        return $command->run($this->getDoctrineInput($command), $this->output->getOutput());
    }
}
