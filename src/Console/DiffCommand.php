<?php

declare(strict_types=1);

namespace LaravelDoctrine\Migrations\Console;

use Doctrine\Migrations\Generator\Exception\NoChangesDetected;
use LaravelDoctrine\Migrations\Configuration\ConfigurationFactory;
use LaravelDoctrine\Migrations\Configuration\DependencyFactoryProvider;

class DiffCommand extends BaseCommand
{
    /**
     * The name and signature of the console command.
     * @var string
     */
    protected $signature = 'doctrine:migrations:diff
    {--em= : For a specific EntityManager. }
    {--filter-expression= : Tables which are filtered by Regular Expression.}
    {--formatted : Format the generated SQL. }
    {--line-length=120 : Max line length of unformatted lines.}
    {--check-database-platform= : Check Database Platform to the generated code.}
    {--allow-empty-diff : Do not throw an exception when no changes are detected. }
    {--from-empty-schema : Generate a full migration as if the current database was empty. }
    ';

    /**
     * @var string
     */
    protected $description = 'Generate a migration by comparing your current database to your mapping information.';

    public function __construct()
    {
        parent::__construct();

        $this->getDefinition()->getOption('check-database-platform')->setDefault(false);
    }

    /**
     * Execute the console command.
     *
     * @param DependencyFactoryProvider $provider
     */
    public function handle(
        DependencyFactoryProvider               $provider,
        ConfigurationFactory                    $configurationFactory
    ): int {
        $dependencyFactory = $provider->fromEntityManagerName($this->option('em'));
        $migrationConfig = $configurationFactory->getConfigAsRepository($this->option('em'));

        $command = new \Doctrine\Migrations\Tools\Console\Command\DiffCommand($dependencyFactory);

        if ($this->input->getOption('filter-expression') === null) {
            $this->input->setOption('filter-expression', $migrationConfig->get('table_storage.schema_filter', $migrationConfig->get('schema.filter')));
        }

        try {
            return $command->run($this->getDoctrineInput($command), $this->output->getOutput());
        } catch (NoChangesDetected $exception) {
            $this->error($exception->getMessage());
            return 0;
        }
    }
}
