<?php

namespace LaravelDoctrine\Migrations\Console;

use Illuminate\Console\Command;
use LaravelDoctrine\Migrations\Configuration\ConfigurationProvider;
use LaravelDoctrine\Migrations\Output\MigrationFileGenerator;

class GenerateCommand extends Command
{
    /**
     * The name and signature of the console command.
     * @var string
     */
    protected $signature = 'doctrine:migrations:generate
    {--connection= : The entity manager connection to generate the migration for.}
    {--create= : The table to be created.}
    {--table= : The table to migrate.}';

    /**
     * @var string
     */
    protected $description = 'Generate a blank migration class.';

    /**
     * Execute the console command.
     *
     * @param ConfigurationProvider  $provider
     * @param MigrationFileGenerator $generator
     */
    public function handle(ConfigurationProvider $provider, MigrationFileGenerator $generator)
    {
        $configuration = $provider->getForConnection($this->option('connection'));

        $filename = $generator->generate(
            $configuration,
            $this->option('create'),
            $this->option('table')
        );

        $this->line(sprintf('<info>Created Migration:</info> %s', $filename));
    }
}
