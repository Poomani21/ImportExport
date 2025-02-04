<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;


class RestartQueueWorker extends Command
{
    protected $signature = 'queue:monitor';
    protected $description = 'Check if queue worker is running and restart if stopped';

    public function handle()
    {

        $this->info("Checking queue worker status...");

        // Run the queue:work command
        $exitCode = Artisan::call('queue:work', [
            '--timeout' => 0,
            '--memory' => 8192,
            '--tries' => 5,
            '--max-jobs' => 5000,
            '--daemon' => true,
        ]);
    
        // Optionally, you can display the output of the command if needed
        $this->info("Queue worker started with exit code: $exitCode");
    
        // You can also display any output from the command
        $output = Artisan::output();
        $this->info("Output: $output");

       
    }
}
