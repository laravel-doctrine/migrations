<?php

declare(strict_types=1);

namespace LaravelDoctrine\Migrations\Configuration;

use Doctrine\Migrations\Configuration\Migration\ConfigurationArray;
use Illuminate\Config\Repository;
use Illuminate\Contracts\Config\Repository as ConfigRepository;
use Illuminate\Contracts\Container\Container;

/**
 * @internal
 */
class ConfigurationFactory
{
    protected ConfigRepository $config;

    protected Container $container;

    public function __construct(ConfigRepository $config, Container $container)
    {
        $this->config = $config;
        $this->container = $container;
    }

    /**
     * @param string|null $name The EntityManager name
     * @return array<string, mixed> The configuration (see config/migrations.php)
     */
    public function getConfig(string $name = null): array
    {
        if ($name && $this->config->has('migrations.' . $name)) {
            return $this->config->get('migrations.' . $name, []);
        }
        return $this->config->get('migrations.default', []);
    }

    public function getConfigAsRepository(string $name = null): Repository
    {
        return new Repository($this->getConfig($name));
    }

    public function make(string $name = null): ConfigurationArray
    {
        $config = $this->getConfigAsRepository($name);

        return new ConfigurationArray([
            'table_storage' => [
                'table_name' => $config->get('table', 'migrations'),
                'version_column_length' => $config->get('version_column_length', 191)
            ],
            'migrations_paths' => [
                $config->get('namespace', 'Database\\Migrations') => $config->get(
                    'directory',
                    database_path('migrations')
                )
            ],
            'organize_migrations' => $config->get('organize_migrations') ?: 'none'
        ]);
    }
}
