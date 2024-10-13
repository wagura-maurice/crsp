<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class Seed extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:seed';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seed the applications core database tables';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            // logic for looping database tables though Orange hill's iseed commands.
            Artisan::call('iseed dublicate_phone_numbers,dublicate_national_numbers --force');
            
            return Command::SUCCESS;
        } catch (\Throwable $th) {
            // throw $th;
            eThrowable(get_class($this), $th->getMessage(), $th->getTraceAsString());
            
            return Command::FAILURE;
        }
    }
}
