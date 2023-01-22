<?php

declare(strict_types=1);

namespace LaravelDoctrine\Migrations\Console;

use LaravelDoctrine\ORM\Console\Command;

class RollbackCommand extends Command
{
    /**
     * The name and signature of the console command.
     * @var string
     */
    protected $signature = 'doctrine:migrations:rollback {version=prev}
    {--em= : For a specific EntityManager. }';

    /**
     * @var string
     */
    protected $description = 'Rollback to the previous migration';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        return $this->call('doctrine:migrations:migrate', [
            'version' => $this->argument('version'),
            '--em' => $this->option('em')
        ]);
    }
}
