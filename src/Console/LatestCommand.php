<?php

declare(strict_types=1);

namespace LaravelDoctrine\Migrations\Console;

use Illuminate\Console\Command;
use LaravelDoctrine\Migrations\Configuration\DependencyFactoryProvider;

class LatestCommand extends Command
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
    public function handle(DependencyFactoryProvider $provider)
    {
        $dependencyFactory = $provider->getForConnection($this->option('connection'));

        $command = new \Doctrine\Migrations\Tools\Console\Command\LatestCommand($dependencyFactory);
        return $command->run($this->input, $this->output->getOutput());
    }
}
