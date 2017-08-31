<?php

namespace LaravelDoctrine\Migrations\Console;

use Illuminate\Console\Command;
use LaravelDoctrine\Migrations\Configuration\ConfigurationProvider;

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
     * @param ConfigurationProvider $provider
     */
    public function handle(ConfigurationProvider $provider)
    {
        $configuration = $provider->getForConnection(
            $this->option('connection')
        );

        $this->line('<info>Latest version:</info> ' . $configuration->getLatestVersion());
    }
}
