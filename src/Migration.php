<?php

namespace LaravelDoctrine\Migrations;

use Doctrine\DBAL\Migrations\Migration as DBALMigration;
use LaravelDoctrine\Migrations\Configuration\Configuration;
use LaravelDoctrine\Migrations\Exceptions\ExecutedUnavailableMigrationsException;
use LaravelDoctrine\Migrations\Exceptions\MigrationVersionException;

class Migration
{
    /**
     * @var DBALMigration
     */
    protected $migration;

    /**
     * @var string|null
     */
    protected $version;

    /**
     * @var Configuration
     */
    protected $configuration;

    /**
     * @param Configuration $configuration
     * @param string        $version
     *
     * @throws ExecutedUnavailableMigrationsException
     */
    public function __construct(Configuration $configuration, $version = 'latest')
    {
        $this->configuration = $configuration;
        $this->makeMigration($configuration);
        $this->setVersion($configuration, $version);
    }

    /**
     * @param Configuration $configuration
     *
     * @return DBALMigration
     */
    protected function makeMigration(Configuration $configuration)
    {
        return $this->migration = new DBALMigration($configuration);
    }

    /**
     * @return DBALMigration
     */
    public function getMigration()
    {
        return $this->migration;
    }

    /**
     * @param Configuration $configuration
     * @param string        $versionAlias
     */
    protected function setVersion(Configuration $configuration, $versionAlias)
    {
        $version = $configuration->resolveVersionAlias($versionAlias);

        if ($version === null || $version === false) {
            if ($versionAlias == 'prev') {
                throw new MigrationVersionException('Already at first version');
            }
            if ($versionAlias == 'next') {
                throw new MigrationVersionException('Already at latest version');
            }

            throw new MigrationVersionException(sprintf('Unknown version: %s', e($versionAlias)));
        }

        $this->version = $version;
    }

    /**
     * @throws ExecutedUnavailableMigrationsException
     */
    public function checkIfNotExecutedUnavailableMigrations()
    {
        $configuration = $this->configuration;

        $executedUnavailableMigrations = array_diff(
            $configuration->getMigratedVersions(),
            $configuration->getAvailableVersions()
        );

        if (count($executedUnavailableMigrations) > 0) {
            throw new ExecutedUnavailableMigrationsException($executedUnavailableMigrations);
        }
    }

    /**
     * @return string|null
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * @return Configuration
     */
    public function getConfiguration()
    {
        return $this->configuration;
    }
}
