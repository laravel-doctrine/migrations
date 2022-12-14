<?php

declare(strict_types=1);

namespace LaravelDoctrine\Migrations\Console;

use LaravelDoctrine\Migrations\Configuration\DependencyFactoryProvider;

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
    {--all : Apply to all the versions.}
    {--range-from= : Apply from specified version. }
    {--range-to= : Apply to specified version. }';

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

}
