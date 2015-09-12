<?php

namespace LaravelDoctrine\Migrations;

class Migrator
{
    /**
     * @var array
     */
    protected $notes = [];

    /**
     * @param Migration  $migration
     * @param bool|false $dryRun
     * @param bool|false $timeQueries
     */
    public function migrate(Migration $migration, $dryRun = false, $timeQueries = false)
    {
        $sql = $migration->getMigration()->migrate(
            $migration->getVersion(),
            $dryRun,
            $timeQueries
        );

        $this->writeNotes($migration, $timeQueries, $sql);
    }

    /**
     * @param Migration   $migration
     * @param string|bool $path
     */
    public function migrateToFile(Migration $migration, $path)
    {
        $path = is_bool($path) ? getcwd() : $path;

        $sql = $migration->getMigration()->writeSqlFile($path, $migration->getVersion());

        $this->writeNotes($migration, false, $sql);
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

        foreach ($sql as $version => $sql) {
            $msg = "<info>Migrated:</info> $version";

            if ($timeQueries) {
                $versionInstance = $migration->getConfiguration()->getVersion($version);
                $msg .= " ({$versionInstance->getTime()}s)";
            }

            $this->notes[] = $msg;
        }
    }
}
