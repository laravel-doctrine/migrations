<?php

namespace LaravelDoctrine\Migrations\Console;

use LaravelDoctrine\Migrations\Configuration\ConfigurationProvider;
use LaravelDoctrine\Migrations\Rollup;
use LaravelDoctrine\ORM\Console\Command;

final class RollupCommand extends Command
{
    protected $signature = 'doctrine:migrations:rollup
    {--connection= : For a specific connection. }
    {--version-name= : A specific version gets marked as migrated. }';

    public function handle(ConfigurationProvider $provider, Rollup $rollup): void
    {
        $version = $rollup->rollup(
            $provider->getForConnection($this->option('connection')),
            $this->option('version-name')
        );

        $this->line(\sprintf('Rolled up migrations to version <info>%s</info>', $version->getVersion()));
    }
}
