<?php

namespace LaravelDoctrine\Migrations\Testing;

use Illuminate\Contracts\Console\Kernel;

trait DatabaseMigrations
{
    /**
     * Define hooks to migrate the database before and after each test.
     *
     * @return void
     */
    public function runDatabaseMigrations()
    {
        $this->artisan('doctrine:migrations:refresh');

        $kernel = $this->app[Kernel::class];
        if (\method_exists($kernel, 'setArtisan')) {
            $kernel->setArtisan(null);
        }

        $this->beforeApplicationDestroyed(function () {
            $this->artisan('doctrine:migrations:rollback');
        });
    }
}
