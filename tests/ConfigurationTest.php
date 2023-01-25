<?php

namespace LaravelDoctrine\Migrations\Tests;

use Doctrine\Migrations\Configuration\Migration\ConfigurationArray;
use Illuminate\Config\Repository;
use LaravelDoctrine\Migrations\Configuration\ConfigurationFactory;
use function database_path;

/**
 * laravel-doctrine allow different configuration for each named EntityManager.
 */
class ConfigurationTest extends \PHPUnit\Framework\TestCase
{

    public function testDefaultConfigurations(): void
    {
        $configRepository = self::makeConfigRepository(require __DIR__ . '/../config/migrations.php');
        $factory = new ConfigurationFactory($configRepository);
        $doctrineConfig = $factory->make(null)->getConfiguration();

        self::assertSame($configRepository->get('migrations.default.table_storage.table_name'), $doctrineConfig->getMetadataStorageConfiguration()->getTableName());
        self::assertSame(191, $doctrineConfig->getMetadataStorageConfiguration()->getVersionColumnLength());
        self::assertSame($configRepository->get('migrations.default.migrations_paths'), $doctrineConfig->getMigrationDirectories());
        self::assertSame(false, $doctrineConfig->areMigrationsOrganizedByYear());
        self::assertSame(false, $doctrineConfig->areMigrationsOrganizedByYearAndMonth());
    }

    public function testDefaultConfigurations2(): void
    {
        $configRepository =self::makeConfigRepository([
            'default' => [
                'table_storage' => [
                    'table_name'     => 'migrations2',

                    'version_column_length' => 192,

                    'schema_filter'    => '/^(?!password_resets|failed_jobs).*$/'
                ],

                'migrations_paths' => [
                    'Database\\Migrations2' => database_path('migrations2')
                ],

                'organize_migrations' => 'year_and_month',
            ],
        ]);

        $factory = new ConfigurationFactory($configRepository);
        $doctrineConfig = $factory->make(null)->getConfiguration();

        self::assertSame($configRepository->get('migrations.default.table_storage.table_name'), $doctrineConfig->getMetadataStorageConfiguration()->getTableName());
        self::assertSame($configRepository->get('migrations.default.table_storage.version_column_length'), $doctrineConfig->getMetadataStorageConfiguration()->getVersionColumnLength());
        self::assertSame($configRepository->get('migrations.default.migrations_paths'), $doctrineConfig->getMigrationDirectories());
        self::assertSame(true, $doctrineConfig->areMigrationsOrganizedByYear());
        self::assertSame(true, $doctrineConfig->areMigrationsOrganizedByYearAndMonth());
    }

    /**
     * This was the default configuration for laravel-doctrine/migrations before version 3.x
     */
    public function testPreviousConfigurationStructure(): void
    {
        $configRepository = self::makeConfigRepository([
            'default' => [
                'table'     => 'migrations',
                'directory' => database_path('migrations'),
                'organize_migrations' => false,
                'namespace' => 'Database\\Migrations',
                'schema'    => [
                    'filter' => '/^(?!password_resets|failed_jobs).*$/'
                ],
                'version_column_length' => 191
            ]
        ]);
        $factory = new ConfigurationFactory($configRepository);
        $doctrineConfig = $factory->make(null)->getConfiguration();

        self::assertSame($configRepository->get('migrations.default.table'), $doctrineConfig->getMetadataStorageConfiguration()->getTableName());
        self::assertSame($configRepository->get('migrations.default.version_column_length'), $doctrineConfig->getMetadataStorageConfiguration()->getVersionColumnLength());
        self::assertSame([$configRepository->get('migrations.default.namespace') => $configRepository->get('migrations.default.directory')], $doctrineConfig->getMigrationDirectories());
        self::assertSame(false, $doctrineConfig->areMigrationsOrganizedByYear());
        self::assertSame(false, $doctrineConfig->areMigrationsOrganizedByYearAndMonth());
    }

    private static function makeConfigRepository(array $config) {
        return new Repository(['migrations' => $config]);
    }
}

