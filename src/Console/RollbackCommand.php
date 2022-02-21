<?php

declare(strict_types=1);

namespace LaravelDoctrine\Migrations\Console;

use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;
use LaravelDoctrine\Migrations\Configuration\DependencyFactoryProvider;

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
     * @param DependencyFactoryProvider $provider
     */
    public function handle(DependencyFactoryProvider $provider)
    {
        $dependencyFactory = $provider->getForConnection(
            $this->option('connection')
        );

        $version = $this->argument('version') ?:
            $dependencyFactory->getVersionAliasResolver()->resolveVersionAlias(""); //TODO: get latest

        if ($version == 0) {
            $this->error('No migrations to be rollbacked');
            return;
        }

        $this->call('doctrine:migrations:execute', [
            'version'      => $version,
            '--connection' => $this->option('connection'),
            '--down'       => true
        ]);
    }
}
