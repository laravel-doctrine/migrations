<?php

declare(strict_types=1);

namespace LaravelDoctrine\Migrations\Configuration;

use Doctrine\Migrations\Configuration\Migration\ConfigurationArray;
use Illuminate\Config\Repository;
use Illuminate\Contracts\Config\Repository as ConfigRepository;
use Illuminate\Contracts\Container\Container;

class ConfigurationFactory
{
    protected ConfigRepository $config;

    protected Container $container;

    public function __construct(ConfigRepository $config, Container $container)
    {
        $this->config = $config;
        $this->container = $container;
    }

    public function getConfigForConnection(string $name = null)
    {
        if ($name && $this->config->has('migrations.' . $name)) {
            return new Repository($this->config->get('migrations.' . $name, []));
        }
        return new Repository($this->config->get('migrations.default', []));
    }

    public function make(string $name = null)
    {
       $config = $this->getConfigForConnection($name);

        return new ConfigurationArray([
            'table_storage' => [
                'table_name' => $config->get('table', 'migrations'),
                'version_column_length' => $config->get('version_column_length', 14)
            ],
            'migrations_paths' => [
                $config->get('namespace', 'Database\\Migrations') => $config->get(
                    'directory',
                    database_path('migrations')
                )
            ]
        ]);
    }
}
