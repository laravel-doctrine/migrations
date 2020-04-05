<?php

namespace LaravelDoctrine\Migrations\Console;

use Doctrine\Migrations\Exception\MigrationException;
use Illuminate\Console\Command;
use InvalidArgumentException;
use LaravelDoctrine\Migrations\Configuration\Configuration;
use LaravelDoctrine\Migrations\Configuration\ConfigurationProvider;

class VersionCommand extends Command
{
    /**
     * The name and signature of the console command.
     * @var string
     */
    protected $signature = 'doctrine:migrations:version {version?}
    {--connection= : For a specific connection.}
    {--add : Add the specified version }
    {--delete : Delete the specified version.}
    {--all : Apply to all the versions.}
    {--range-from= : Apply from specified version. }
    {--range-to= : Apply to specified version. }';

    /**
     * @var string
     */
    protected $description = 'Manually add and delete migration versions from the version table.';

    /**
     * @var bool
     */
    protected $markMigrated;

    /**
     * @var Configuration
     */
    protected $configuration;

    /**
     * Execute the console command.
     *
     * @param ConfigurationProvider $provider
     */
    public function handle(ConfigurationProvider $provider)
    {
        $this->configuration = $provider->getForConnection(
            $this->option('connection')
        );

        if (!$this->option('add') && !$this->option('delete')) {
            return $this->error('You must specify whether you want to --add or --delete the specified version.');
        }

        $this->markMigrated = (boolean) $this->option('add');

        if ($this->input->isInteractive()) {
            $question = 'WARNING! You are about to add, delete or synchronize migration versions from the version table that could result in data lost. Are you sure you wish to continue? (y/n)';
            if ($this->confirm($question)) {
                $this->markVersions();
            } else {
                $this->error('Migration cancelled!');
            }
        } else {
            $this->markVersions();
        }
    }

    /**
     * @throws MigrationException
     */
    private function markVersions()
    {
        $affectedVersion = $this->argument('version');

        $allOption       = $this->option('all');
        $rangeFromOption = $this->option('range-from');
        $rangeToOption   = $this->option('range-to');

        if ($allOption && ($rangeFromOption !== null || $rangeToOption !== null)) {
            throw new InvalidArgumentException('Options --all and --range-to/--range-from both used. You should use only one of them.');
        } elseif ($rangeFromOption !== null ^ $rangeToOption !== null) {
            throw new InvalidArgumentException('Options --range-to and --range-from should be used together.');
        }

        if ($allOption === true) {
            $availableVersions = $this->configuration->getAvailableVersions();
            foreach ($availableVersions as $version) {
                $this->mark($version, true);
            }
        } elseif ($rangeFromOption !== null && $rangeToOption !== null) {
            $availableVersions = $this->configuration->getAvailableVersions();
            foreach ($availableVersions as $version) {
                if ($version >= $rangeFromOption && $version <= $rangeToOption) {
                    $this->mark($version, true);
                }
            }
        } else {
            $this->mark($affectedVersion);
        }
    }

    /**
     * @param            $versionName
     * @param bool|false $all
     *
     * @throws MigrationException
     */
    protected function mark($versionName, $all = false)
    {
        if (!$this->configuration->hasVersion($versionName)) {
            throw MigrationException::unknownMigrationVersion($versionName);
        }

        $version = $this->configuration->getVersion($versionName);

        if ($this->markMigrated && $this->configuration->hasVersionMigrated($version)) {
            $marked = true;
            if (!$all) {
                throw new InvalidArgumentException(sprintf('The version "%s" already exists in the version table.',
                    $version));
            }
        }

        if (!$this->markMigrated && !$this->configuration->hasVersionMigrated($version)) {
            $marked = false;
            if (!$all) {
                throw new InvalidArgumentException(sprintf('The version "%s" does not exists in the version table.',
                    $version));
            }
        }

        if (!isset($marked)) {
            $filename = $this->configuration->getNamingStrategy()->getFilename($versionName);
            if ($this->markMigrated) {
                $version->markMigrated();
                $this->info('<info>Added version to table:</info> ' . $filename);
            } else {
                $version->markNotMigrated();
                $this->info('<info>Removed version from table:</info> ' . $filename);
            }
        }
    }
}
