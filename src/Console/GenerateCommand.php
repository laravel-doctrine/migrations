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
    protected $signature = 'doctrine:migrations:generate {name}
    {--connection= : For a specific connection }';

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
    public function fire(ConfigurationProvider $provider, MigrationFileGenerator $generator)
    {
        $name = $this->option('connection') ?: null;

        $configuration = $provider->getForConnection($name);

        $filename = $generator->generate(
            $this->argument('name'),
            $configuration
        );

        $this->line(sprintf('<info>Created Migration:</info> %s', $filename));
    }
}
