<?php

declare(strict_types=1);

namespace LaravelDoctrine\Migrations\Console;

use Illuminate\Console\Command;

class RefreshCommand extends Command
{
    /**
     * The name and signature of the console command.
     * @var string
     */
    protected $signature = 'doctrine:migrations:refresh
    {--em= : For a specific EntityManager. }';

    /**
     * @var string
     */
    protected $description = 'Reset and re-run all migrations';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $resetReturn = $this->call('doctrine:migrations:reset', [
            '--em' => $this->option('em')
        ]);

        if ($resetReturn !== 0) {
            return 1;
        }

        return $this->call('doctrine:migrations:migrate', [
            '--em' => $this->option('em')
        ]);
    }
}
