<?php

declare(strict_types=1);

namespace LaravelDoctrine\Migrations\Configuration;

use Doctrine\DBAL\Connection;
use Doctrine\Migrations\Configuration\Connection\ExistingConnection;
use Doctrine\Migrations\Configuration\Migration\ConfigurationLoader;
use Doctrine\Migrations\DependencyFactory;
use Doctrine\Persistence\ManagerRegistry;

class DependencyFactoryProvider
{
    protected ManagerRegistry $registry;

    protected ConfigurationFactory $factory;

    public function __construct(ManagerRegistry $registry, ConfigurationFactory $factory)
    {
        $this->registry = $registry;
        $this->factory  = $factory;
    }

    /**
     * @param string|null $name
     *
     * @return ConfigurationLoader
     */
    public function getForConnection(string $name = null): DependencyFactory
    {
        /** @var Connection $connection */
        $connection = $this->registry->getConnection($name);
        $configuration = $this->factory->make($name);
        return DependencyFactory::fromConnection($configuration, new ExistingConnection($connection));
    }
}
