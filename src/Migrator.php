<?php

namespace LaravelDoctrine\Migrations;

use Doctrine\Migrations\Exception\MigrationException;
use Doctrine\Migrations\MigratorConfiguration;
use Doctrine\Migrations\Version\Version;

class Migrator
{
    /**
     * @var array
     */
    protected $notes = [];

	/**
	 * @param Migration $migration
	 * @param bool|false $dryRun
	 * @param bool|false $timeQueries
	 * @param bool|false $allowNoMigration
	 * @throws MigrationException
	 */
    public function migrate(Migration $migration, $dryRun = false, $timeQueries = false, bool $allowNoMigration = false)
    {
        $configuration = $this->setConfiguration($dryRun, $timeQueries, $allowNoMigration);

        $sql = $migration->getMigration()->migrate(
            $migration->getVersion(),
            $configuration
        );

        $this->writeNotes($migration, $timeQueries, $sql);
    }

    /**
     * @param Version    $version
     * @param            $direction
     * @param bool|false $dryRun
     * @param bool|false $timeQueries
     */
    public function execute(Version $version, $direction, $dryRun = false, $timeQueries = false)
    {
        $configuration = $this->setConfiguration($dryRun, $timeQueries);
        $version->execute($direction, $configuration);

        $verb = $direction === 'down' ? 'Rolled back' : 'Migrated';

        $this->note($version->getVersion(), $version, $timeQueries, $verb);
    }

    /**
     * @param Migration   $migration
     * @param string|bool $path
     */
    public function migrateToFile(Migration $migration, $path)
    {
        $path = is_bool($path) ? getcwd() : $path;

        $sql = $migration->getMigration()->getSql($migration->getVersion());
        $migration->getMigration()->writeSqlFile($path, $migration->getVersion());

        $this->writeNotes($migration, false, $sql);
    }

    /**
     * @param Version $version
     * @param         $direction
     * @param         $path
     */
    public function executeToFile(Version $version, $direction, $path)
    {
        $path = is_bool($path) ? getcwd() : $path;

        $version->writeSqlFile($path, $direction);

        $verb = $direction === 'down' ? 'Rolled back' : 'Migrated';

        $this->note($version->getVersion(), $version, false, $verb);
    }

    /**
     * @return array
     */
    public function getNotes()
    {
        return $this->notes;
    }

    /**
     * @param Migration $migration
     * @param           $timeQueries
     * @param           $sql
     */
    protected function writeNotes(Migration $migration, $timeQueries, $sql)
    {
        if (count($sql) < 1) {
            $this->notes[] = '<info>Nothing to migrate.</info>';
        }

        foreach ($sql as $versionName => $sql) {
            $this->note(
                $versionName,
                $migration->getConfiguration()->getVersion($versionName),
                $timeQueries
            );
        }
    }

    /**
     * @param         $versionName
     * @param Version $version
     * @param bool    $timeQueries
     * @param string  $verb
     */
    protected function note($versionName, Version $version, $timeQueries = false, $verb = 'Migrated')
    {
        $msg = "<info>{$verb}:</info> $versionName";

        if ($timeQueries) {
            $msg .= " ({$version->getTime()}s)";
        }

        $this->notes[] = $msg;
    }

    /**
     * @param bool $dryRun
     * @param bool $timeQueries
     * @param bool $allowNoMigrations
     * @return MigratorConfiguration
     */
    private function setConfiguration(bool $dryRun = false, bool $timeQueries = false, bool $allowNoMigrations = false)
    {
        $configuration = new MigratorConfiguration();
        $configuration->setDryRun($dryRun);
        $configuration->setTimeAllQueries($timeQueries);
        $configuration->setNoMigrationException($allowNoMigrations);
        return $configuration;
    }
}
