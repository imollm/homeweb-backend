<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Class CreateDatabase
 * @package App\Console\Commands
 */
class CreateDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:database {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new empty database';

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
     * @return void
     */
    public function handle(): void
    {
        if (is_string($schemaName =  $this->argument('name'))) {

            $query = "CREATE DATABASE IF NOT EXISTS $schemaName;";

            $result = DB::connection('mysql')->statement($query);

            $message = $result ? "Database $schemaName was created successfully!" : "Something went wrong when creating database $schemaName";

            $this->info($message);

        } else {

            $this->info("No database name provided");

        }
    }

}
