<?php

declare(strict_types=1);

namespace LaravelDoctrine\Migrations\Configuration;

use Doctrine\Migrations\Configuration\EntityManager\ExistingEntityManager;
use Doctrine\Migrations\DependencyFactory;
use Doctrine\Persistence\ManagerRegistry;

class DependencyFactoryProvider
{
    protected ManagerRegistry $registry;
    protected ConfigurationFactory $factory;

    public function __construct(
        ManagerRegistry        $registry,
        ConfigurationFactory   $factory
    ) {
        $this->registry = $registry;
        $this->factory = $factory;
    }

    /**
     * @param string|null $name
     *
     * @return DependencyFactory
     */
    public function fromEntityManagerName(?string $name = null): DependencyFactory
    {
        $configuration = $this->factory->make($name);
        return DependencyFactory::fromEntityManager(
            $configuration,
            new ExistingEntityManager($this->registry->getManager($name))
        );
    }
}
