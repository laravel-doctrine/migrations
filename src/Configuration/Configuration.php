<?php

namespace LaravelDoctrine\Migrations\Configuration;

use Doctrine\Migrations\Configuration\Configuration as MigrationsConfiguration;
use LaravelDoctrine\Migrations\Naming\NamingStrategy;

class Configuration extends MigrationsConfiguration
{
    /**
     * @var NamingStrategy
     */
    protected $namingStrategy;

    /**
     * @return NamingStrategy
     */
    public function getNamingStrategy()
    {
        return $this->namingStrategy;
    }

    /**
     * @param NamingStrategy $namingStrategy
     */
    public function setNamingStrategy(NamingStrategy $namingStrategy)
    {
        $this->namingStrategy = $namingStrategy;
    }
}
