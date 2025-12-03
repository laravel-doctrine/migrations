<?php

declare(strict_types=1);

namespace LaravelDoctrine\Migrations\Console;

use LaravelDoctrine\Migrations\Configuration\DependencyFactoryProvider;
use Symfony\Component\Console\Input\InputOption;

class VersionCommand extends BaseCommand
{
    /**
     * The name and signature of the console command.
     * @var string
     */
    protected $signature = 'doctrine:migrations:version {version?}
    {--em= : For a specific EntityManager. }
    {--add : Add the specified version }
    {--delete : Delete the specified version.}
    {--all : Apply to all the versions.}';

    /**
     * @var string
     */
    protected $description = 'Manually add and delete migration versions from the version table.';

    /**
     * Execute the console command.
     *
     * @param DependencyFactoryProvider $provider
     */
    public function handle(DependencyFactoryProvider $provider): int
    {
        $dependencyFactory = $provider->fromEntityManagerName($this->option('em'));

        $command = new \Doctrine\Migrations\Tools\Console\Command\VersionCommand($dependencyFactory);
        return $command->run($this->getDoctrineInput($command), $this->output->getOutput());
    }

    protected function configureUsingFluentDefinition(): void
    {
        parent::configureUsingFluentDefinition();
        $this->getDefinition()->addOption(
            new InputOption(
                'range-from',
                null,
                InputOption::VALUE_REQUIRED,
                'Apply from specified version.',
            )
        );
        $this->getDefinition()->addOption(
            new InputOption(
                'range-to',
                null,
                InputOption::VALUE_REQUIRED,
                'Apply to specified version. ',
            )
        );
    }
}
