<?php

declare(strict_types=1);

namespace LaravelDoctrine\Migrations\Configuration;

use Doctrine\Migrations\Configuration\Migration\ConfigurationArray;
use Illuminate\Config\Repository;
use Illuminate\Contracts\Config\Repository as ConfigRepository;
use Illuminate\Contracts\Container\Container;
use function array_merge;
use function database_path;

/**
 * @internal
 */
class ConfigurationFactory
{
    protected ConfigRepository $config;

    public function __construct(ConfigRepository $config)
    {
        $this->config = $config;
    }

    /**
     * @param string|null $name The EntityManager name
     * @return array<string, mixed> The configuration (see config/migrations.php)
     */
    public function getConfig(?string $name = null): array
    {
        if ($name && $this->config->has('migrations.' . $name)) {
            return $this->config->get('migrations.' . $name, []);
        }
        return $this->config->get('migrations.default', []);
    }

    public function getConfigAsRepository(?string $name = null): Repository
    {
        return new Repository($this->getConfig($name));
    }

    public function make(?string $name = null): ConfigurationArray
    {
        $config = $this->getConfigAsRepository($name);

        $configAsArray = array_merge($config->all(), [
            'table_storage' =>  $config->get('table_storage', [
                'table_name' => $config->get('table', 'migrations'),
                'version_column_length' => $config->get('version_column_length', 191)
            ]),
            'migrations_paths' => $config->get('migrations_paths', [
                $config->get('namespace', 'Database\\Migrations') => $config->get(
                    'directory',
                    database_path('migrations')
                )
            ]),
            'organize_migrations' => $config->get('organize_migrations') ?: 'none'
        ]);

        // Unset previous laravel-doctrine configuration structure
        unset(
            $configAsArray['table_storage']['schema_filter'],
            $configAsArray['table'],
            $configAsArray['directory'],
            $configAsArray['namespace'],
            $configAsArray['schema'],
            $configAsArray['version_column_length']
        );

        return new ConfigurationArray($configAsArray);
    }
}
