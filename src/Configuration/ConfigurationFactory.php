<?php

namespace LaravelDoctrine\Migrations\Configuration;

use Doctrine\DBAL\Connection;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Container\Container;
use Illuminate\Support\Collection;
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
     * @param string     $name
     *
     * @return Configuration
     */
    public function make(Connection $connection, $name = null)
    {
        if ($name && $this->config->has('migrations.' . $name)) {
            $config = new Collection($this->config->get('migrations.' . $name, []));
        } else {
            $config = new Collection($this->config->get('migrations.default', []));
        }

        $configuration = new Configuration($connection);
        $configuration->setName($config->get('name', 'Doctrine Migrations'));
        $configuration->setMigrationsNamespace($config->get('namespace', 'Database\\Migrations'));
        $configuration->setMigrationsTableName($config->get('table', 'migrations'));

        $configuration->getConnection()->getConfiguration()->setFilterSchemaAssetsExpression(
            $config->get('schema.filter', '/^(?).*$/')
        );

        $configuration->setNamingStrategy($this->container->make(
            $config->get('naming_strategy', DefaultNamingStrategy::class)
        ));

        $configuration->setMigrationsFinder($configuration->getNamingStrategy()->getFinder());

        $directory = $config->get('directory', database_path('migrations'));
        $configuration->setMigrationsDirectory($directory);
        $configuration->registerMigrationsFromDirectory($directory);

        return $configuration;
    }
}
