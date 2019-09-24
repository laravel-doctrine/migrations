<?php

namespace LaravelDoctrine\Migrations;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use LaravelDoctrine\Migrations\Configuration\ConfigurationProvider;
use LaravelDoctrine\Migrations\Console\DiffCommand;
use LaravelDoctrine\Migrations\Console\ExecuteCommand;
use LaravelDoctrine\Migrations\Console\GenerateCommand;
use LaravelDoctrine\Migrations\Console\LatestCommand;
use LaravelDoctrine\Migrations\Console\MigrateCommand;
use LaravelDoctrine\Migrations\Console\RefreshCommand;
use LaravelDoctrine\Migrations\Console\ResetCommand;
use LaravelDoctrine\Migrations\Console\RollbackCommand;
use LaravelDoctrine\Migrations\Console\StatusCommand;
use LaravelDoctrine\Migrations\Console\VersionCommand;

class MigrationsServiceProvider extends ServiceProvider
{
    /**
     * @var bool
     */
    protected $defer = true;

    /**
     * Boot the service provider.
     * @return void
     */
    public function boot()
    {
        if (!$this->isLumen()) {
            $this->publishes([
                $this->getConfigPath() => config_path('migrations.php'),
            ], 'config');
        }
    }

    /**
     * Register the service provider.
     * @return void
     */
    public function register()
    {
        $this->mergeConfig();

        $this->commands([
            DiffCommand::class,
            ResetCommand::class,
            LatestCommand::class,
            StatusCommand::class,
            MigrateCommand::class,
            ExecuteCommand::class,
            VersionCommand::class,
            RefreshCommand::class,
            RollbackCommand::class,
            GenerateCommand::class
        ]);
    }

    /**
     * Merge config
     */
    protected function mergeConfig()
    {
        if ($this->isLumen()) {
            $this->app->configure('migrations');
        }

        $this->mergeConfigFrom(
            $this->getConfigPath(), 'migrations'
        );
    }

    /**
     * @return string
     */
    protected function getConfigPath()
    {
        return __DIR__ . '/../config/migrations.php';
    }

    /**
     * @return array
     */
    public function provides()
    {
        return [
            ConfigurationProvider::class
        ];
    }

    /**
     * @return bool
     */
    protected function isLumen()
    {
        return Str::contains($this->app->version(), 'Lumen');
    }
}
