<?php

declare(strict_types=1);

namespace LaravelDoctrine\Migrations\Configuration;

use Doctrine\DBAL\Connection;
use Doctrine\Persistence\ManagerRegistry;

class ConfigurationProvider
{
    /**
     * @var ManagerRegistry
     */
    protected $registry;

    /**
     * @var ConfigurationFactory
     */
    protected $factory;

    /**
     * @param ManagerRegistry      $registry
     * @param ConfigurationFactory $factory
     */
    public function __construct(ManagerRegistry $registry, ConfigurationFactory $factory)
    {
        $this->registry = $registry;
        $this->factory  = $factory;
    }

    /**
     * @param string|null $name
     *
     * @return Configuration|string
     */
    public function getForConnection($name = null)
    {
        /** @var Connection $connection */
        $connection = $this->registry->getConnection($name);

        return $this->factory->make($connection, $name);
    }
}
