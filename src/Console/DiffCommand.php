<?php

declare(strict_types=1);

namespace LaravelDoctrine\Migrations\Console;

use Doctrine\Persistence\ManagerRegistry;
use LaravelDoctrine\Migrations\Configuration\ConfigurationFactory;
use LaravelDoctrine\Migrations\Configuration\DependencyFactoryProvider;

class DiffCommand extends BaseCommand
{
    /**
     * The name and signature of the console command.
     * @var string
     */
    protected $signature = 'doctrine:migrations:diff
    {--connection= : For a specific connection }
    {--filter-expression= : Tables which are filtered by Regular Expression.}';

    /**
     * @var string
     */
    protected $description = 'Generate a migration by comparing your current database to your mapping information.';

    /**
     * Execute the console command.
     *
     * @param DependencyFactoryProvider $provider
     */
    public function handle(
        DependencyFactoryProvider               $provider,
        ConfigurationFactory                    $configurationFactory,
        ManagerRegistry $registry
    ): int {
        $dependencyFactory = $provider->fromConnectionName($this->option('connection'));
        $migrationConfig = $configurationFactory->getConfigAsRepository($this->option('connection'));

        $command = new \Doctrine\Migrations\Tools\Console\Command\DiffCommand($dependencyFactory);

        if ($this->input->getOption('filter-expression') === null) {
            $this->input->setOption('filter-expression', $migrationConfig->get('schema.filter'));
        }

        return $command->run($this->getDoctrineInput($command), $this->output->getOutput());
    }
}
