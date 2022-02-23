<?php

declare(strict_types=1);

namespace LaravelDoctrine\Migrations\Configuration;

use Doctrine\DBAL\Connection;
use Doctrine\Migrations\Configuration\Connection\ExistingConnection;
use Doctrine\Migrations\Configuration\EntityManager\ExistingEntityManager;
use Doctrine\Migrations\DependencyFactory;
use Doctrine\ORM\Tools\Setup;
use Doctrine\Persistence\ManagerRegistry;
use Illuminate\Config\Repository;
use Illuminate\Container\Container;
use LaravelDoctrine\ORM\Configuration\Cache\CacheManager;
use LaravelDoctrine\ORM\Configuration\Connections\ConnectionManager;
use LaravelDoctrine\ORM\Configuration\MetaData\MetaDataManager;
use LaravelDoctrine\ORM\EntityManagerFactory;
use LaravelDoctrine\ORM\Resolvers\EntityListenerResolver;

class DependencyFactoryProvider
{
    protected ManagerRegistry $registry;
    protected ConfigurationFactory $factory;
    protected Container $container;
    protected Setup $setup;
    protected MetaDataManager $meta;
    protected ConnectionManager $connection;
    protected CacheManager $cache;
    protected Repository $config;
    protected EntityListenerResolver $resolver;

    public function __construct(
        ManagerRegistry        $registry,
        ConfigurationFactory   $factory,
        Repository             $config,
        Container              $container,
        Setup                  $setup,
        MetaDataManager        $meta,
        ConnectionManager      $connection,
        CacheManager           $cache,
        EntityListenerResolver $resolver
    ) {
        $this->registry = $registry;
        $this->factory = $factory;
        $this->container = $container;
        $this->setup = $setup;
        $this->meta = $meta;
        $this->connection = $connection;
        $this->cache = $cache;
        $this->config = $config;
        $this->resolver = $resolver;
    }

    public function getEntityManager(string $connection = null, string $em = null): DependencyFactory
    {
        $emSettings = $this->config->get('doctrine.managers.' . $em ?? $this->registry->getDefaultManagerName());
        $emSettings['connection'] = $connection ?? $this->config->get('database.default');

        $emf = new EntityManagerFactory(
            $this->container,
            $this->setup,
            $this->meta,
            $this->connection,
            $this->cache,
            $this->config,
            $this->resolver
        );

        return DependencyFactory::fromEntityManager(
            $this->factory->make(),
            new ExistingEntityManager($emf->create($emSettings))
        );
    }

    /**
     * @param string|null $connectionName
     *
     * @return DependencyFactory
     */
    public function getConnection(string $connectionName = null): DependencyFactory
    {
        /** @var Connection $connection */
        $connection = $this->registry->getConnection($connectionName);
        $configuration = $this->factory->make($connectionName);
        return DependencyFactory::fromConnection($configuration, new ExistingConnection($connection));
    }
}
