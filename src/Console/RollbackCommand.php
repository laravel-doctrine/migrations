<?php

namespace LaravelDoctrine\Migrations\Console;

use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;
use LaravelDoctrine\Migrations\Configuration\ConfigurationProvider;

class RollbackCommand extends Command
{
    use ConfirmableTrait;

    /**
     * The name and signature of the console command.
     * @var string
     */
    protected $signature = 'doctrine:migrations:rollback {version?}
    {--connection= : For a specific connection.}';

    /**
     * @var string
     */
    protected $description = 'Rollback to the previous migration';

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

        $version = $this->argument('version') ?: $configuration->getCurrentVersion();

        if ($version == 0) {
            return $this->error('No migrations to be rollbacked');
        }

        $this->call('doctrine:migrations:execute', [
            'version'      => $version,
            '--connection' => $this->option('connection'),
            '--down'       => true
        ]);
    }
}
