<?php

declare(strict_types=1);

namespace LaravelDoctrine\Migrations\Console;

use Illuminate\Console\ConfirmableTrait;
use LaravelDoctrine\Migrations\Configuration\DependencyFactoryProvider;
use LaravelDoctrine\ORM\Console\Command;

class RollbackCommand extends Command
{
    /**
     * The name and signature of the console command.
     * @var string
     */
    protected $signature = 'doctrine:migrations:rollback {version?}
    {--connection= : For a specific connection.}';

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
            'version' => 'prev',
            '--connection' => $this->option('connection')
        ]);
    }
}
