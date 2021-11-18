<?php

namespace LaravelDoctrine\Migrations;

use Doctrine\Migrations\Exception\RollupFailed;
use Doctrine\Migrations\Exception\UnknownMigrationVersion;
use Doctrine\Migrations\Version\Version;
use LaravelDoctrine\Migrations\Configuration\Configuration;

final class Rollup
{
    public function rollup(Configuration $configuration, string $versionName = null): Version
    {
        $version = $this->getVersionToMarkMigrated($configuration, $versionName);

        $configuration->getConnection()->executeQuery(
            \sprintf('DELETE FROM %s', $configuration->getMigrationsTableName())
        );

        $version->markMigrated();

        return $version;
    }

    private function getVersionToMarkMigrated(Configuration $configuration, string $versionName = null): Version
    {
        if (!empty($versionName)) {
            try {
                return $configuration->getDependencyFactory()->getMigrationRepository()->getVersion($versionName);
            } catch (UnknownMigrationVersion $e) {
                throw RollupFailed::noMigrationsFound();
            }
        }

        $versions = $configuration->getDependencyFactory()->getMigrationRepository()->getVersions();
        if (count($versions) === 0) {
            throw RollupFailed::noMigrationsFound();
        }
        if (count($versions) > 1) {
            throw RollupFailed::tooManyMigrations();
        }

        return \current($versions);
    }
}
