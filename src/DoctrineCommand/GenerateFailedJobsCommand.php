<?php

declare(strict_types=1);

namespace LaravelDoctrine\Migrations\DoctrineCommand;

use Doctrine\Migrations\Tools\Console\Command\DoctrineCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use LaravelDoctrine\ORM\Queue\FailedJobTable;

class GenerateFailedJobsCommand extends DoctrineCommand
{
    /** @var string|null */
    protected static $defaultName = 'doctrine:migrations:queue-failed-table';

    protected const UP_TEMPLATE = <<<'UPTEMPLATE'
$builder = (new \LaravelDoctrine\Migrations\Schema\Builder($schema));
$builder->create('<tableName>', function (\LaravelDoctrine\Migrations\Schema\Table $table) {
    $table->increments('id');
    $table->string('uuid');
    $table->string('connection');
    $table->string('queue');
    $table->text('payload');
    $table->dateTime('failed_at');
    $table->text('exception')->setNotnull(false);
    $table->unique(['uuid'], 'uuid_unique');
});
UPTEMPLATE;

    protected const DOWN_TEMPLATE = <<<'DOWN_TEMPLATE'
$schema->dropTable('<tableName>');
DOWN_TEMPLATE;

    protected function configure(): void
    {
        $this
            ->setAliases(['failed-table'])
            ->setDescription('Create a migration for the failed queue jobs database table')
            ->addOption(
                'table',
                null,
                InputOption::VALUE_REQUIRED,
                'Table name',
                'failed_jobs'
            )
            ->addOption(
                'namespace',
                null,
                InputOption::VALUE_REQUIRED,
                'The namespace to use for the migration (must be in the list of configured namespaces)'
            );

        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $configuration = $this->getDependencyFactory()->getConfiguration();

        $migrationGenerator = $this->getDependencyFactory()->getMigrationGenerator();

        $namespace = $input->getOption('namespace');
        if ($namespace === '') {
            $namespace = null;
        }

        $dirs = $configuration->getMigrationDirectories();
        if ($namespace === null) {
            $namespace = key($dirs);
        } elseif (! isset($dirs[$namespace])) {
            throw new Exception(sprintf('Path not defined for the namespace %s', $namespace));
        }

        $tableName = $input->getOption('table');

        assert(is_string($namespace));
        assert(is_string($tableName));

        $fqcn = $this->getDependencyFactory()->getClassNameGenerator()->generateClassName($namespace);

        $up = $this->upScript($tableName);
        $down = $this->downScript($tableName);
        $path = $migrationGenerator->generateMigration($fqcn, $up, $down);

        $this->io->text([
            sprintf('Generated new migration class to "<info>%s</info>"', $path),
            '',
        ]);

        return 0;
    }

    protected function upScript($tableName): string
    {
        return strtr(static::UP_TEMPLATE, [
            '<tableName>' => $tableName,
        ]);
    }

    protected function downScript($tableName): string
    {
        return strtr(static::DOWN_TEMPLATE, [
            '<tableName>' => $tableName,
        ]);
    }
}
