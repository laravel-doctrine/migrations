<?php

namespace LaravelDoctrine\Migrations\Console;

use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Migrations\Provider\OrmSchemaProvider;
use Doctrine\ORM\EntityManagerInterface;
use Illuminate\Console\Command;
use LaravelDoctrine\Migrations\Configuration\ConfigurationProvider;
use LaravelDoctrine\Migrations\Output\MigrationFileGenerator;
use LaravelDoctrine\Migrations\Output\SqlBuilder;

class DiffCommand extends Command
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
     * @param ConfigurationProvider  $provider
     * @param ManagerRegistry        $registry
     * @param SqlBuilder             $builder
     * @param MigrationFileGenerator $generator
     */
    public function handle(
        ConfigurationProvider $provider,
        ManagerRegistry $registry,
        SqlBuilder $builder,
        MigrationFileGenerator $generator
    ) {
        $configuration = $provider->getForConnection($this->option('connection'));
        $em            = $registry->getManager($this->option('connection'));
        $connection    = $configuration->getConnection();

        // Overrule the filter
        if ($filterExpr = $this->option('filter-expression')) {
            $connection->getConfiguration()->setFilterSchemaAssetsExpression($filterExpr);
        }

        $fromSchema = $connection->getSchemaManager()->createSchema();
        $toSchema   = $this->getSchemaProvider($em)->createSchema();

        // Drop tables which don't suffice to the filter regex
        if ($filterExpr = $connection->getConfiguration()->getFilterSchemaAssetsExpression()) {
            foreach ($toSchema->getTables() as $table) {
                $tableName = $table->getName();
                if (!preg_match($filterExpr, $this->resolveTableName($tableName))) {
                    $toSchema->dropTable($tableName);
                }
            }
        }

        $up   = $builder->up($configuration, $fromSchema, $toSchema);
        $down = $builder->down($configuration, $fromSchema, $toSchema);

        if (!$up && !$down) {
            return $this->error('No changes detected in your mapping information.');
        }

        $path = $generator->generate(
            $configuration,
            false,
            false,
            $up,
            $down
        );

        $this->line(sprintf('Generated new migration class to "<info>%s</info>" from schema differences.', $path));
    }

    /**
     * @param EntityManagerInterface $em
     *
     * @return OrmSchemaProvider
     */
    protected function getSchemaProvider(EntityManagerInterface $em)
    {
        return new OrmSchemaProvider($em);
    }

    /**
     * Resolve a table name from its fully qualified name. The `$name` argument
     * comes from Doctrine\DBAL\Schema\Table#getName which can sometimes return
     * a namespaced name with the form `{namespace}.{tableName}`. This extracts
     * the table name from that.
     *
     * @param string $name
     *
     * @return string
     */
    protected function resolveTableName($name)
    {
        $pos = strpos($name, '.');

        return false === $pos ? $name : substr($name, $pos + 1);
    }
}
