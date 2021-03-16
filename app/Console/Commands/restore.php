<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class restore extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:restore';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migration + seeders + passport install';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        shell_exec('php artisan migrate:fresh --seed');
        shell_exec('php artisan passport:install');
    }
}
