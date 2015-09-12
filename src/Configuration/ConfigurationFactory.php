<?php

namespace LaravelDoctrine\Migrations\Configuration;

use Doctrine\DBAL\Connection;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Container\Container;
use LaravelDoctrine\Migrations\Naming\LaravelNamingStrategy;

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
     *
     * @internal param NamingStrategy $namingStrategy
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

        $configuration->setName($this->config->get('migrations.name', 'DoctrineMigrations'));
        $configuration->setMigrationsNamespace($this->config->get('migrations.namespace', 'Database\\Migrations'));
        $configuration->setMigrationsTableName($this->config->get('migrations.table', 'migrations'));

        $configuration->setNamingStrategy($this->container->make(
            $this->config->get('migrations.naming_strategy', LaravelNamingStrategy::class)
        ));

        $configuration->setMigrationFinder($configuration->getNamingStrategy()->getFinder());

        $directory = $this->config->get('migrations.directory', database_path('migrations'));
        $configuration->setMigrationsDirectory($directory);
        $configuration->registerMigrationsFromDirectory($directory);

        return $configuration;
    }
}
