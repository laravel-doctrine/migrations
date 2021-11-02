<?php

namespace LaravelDoctrine\Migrations\Console;

use LaravelDoctrine\Migrations\Configuration\ConfigurationProvider;
use LaravelDoctrine\Migrations\SchemaDumper;
use LaravelDoctrine\ORM\Console\Command;

final class SchemaDumpCommand extends Command
{
    protected $signature = 'doctrine:migrations:dump-schema
    {--connection= : For a specific connection. }
    {--formatted : Format the generated SQL file. }
    {--line-length=120 : Max line length of unformatted lines. }';

    public function handle(ConfigurationProvider $provider, SchemaDumper $schemaDumper): void
    {
        $fileName = $schemaDumper->dump(
            $provider->getForConnection($this->option('connection')),
            $this->option('formatted'),
            $this->option('line-length')
        );

        $this->line(\sprintf('Dumped schema to %s.', $fileName));
    }
}
