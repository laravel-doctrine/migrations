<?php

namespace LaravelDoctrine\Migrations\Configuration;

use Doctrine\DBAL\Connection;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Container\Container;
use LaravelDoctrine\Migrations\Naming\DefaultNamingStrategy;

class ConfigurationFactory
{
    /**
     * @var Repository
     */
    protected $config;

    /**
     * @var Container
     */
    protected $container;

    /**
     * @param Repository $config
     * @param Container  $container
     */
    public function __construct(Repository $config, Container $container)
    {
        $this->config    = $config;
        $this->container = $container;
    }

    /**
     * @param Connection $connection
     *
     * @return Configuration
     */
    public function make(Connection $connection)
    {
        $configuration = new Configuration($connection);

        $configuration->setName($this->config->get('migrations.name', 'Doctrine Migrations'));
        $configuration->setMigrationsNamespace($this->config->get('migrations.namespace', 'Database\\Migrations'));
        $configuration->setMigrationsTableName($this->config->get('migrations.table', 'migrations'));

        $configuration->getConnection()->getConfiguration()->setFilterSchemaAssetsExpression(
            $this->config->get('migrations.schema.filter', '/^(?).*$/')
        );

        $configuration->setNamingStrategy($this->container->make(
            $this->config->get('migrations.naming_strategy', DefaultNamingStrategy::class)
        ));

        $configuration->setMigrationsFinder($configuration->getNamingStrategy()->getFinder());

        $directory = $this->config->get('migrations.directory', database_path('migrations'));
        $configuration->setMigrationsDirectory($directory);
        $configuration->registerMigrationsFromDirectory($directory);

        return $configuration;
    }
}
