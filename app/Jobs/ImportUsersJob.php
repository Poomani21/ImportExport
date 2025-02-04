<?php

namespace App\Jobs;

use App\Imports\UsersImport;
use App\Models\ImportJobStatus;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class ImportUsersJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $filePath;
    protected $jobId;
    public function __construct($filePath,$jobId)
    {
        $this->filePath = $filePath;
        $this->jobId=$jobId;
    }

    public function handle()
    {
    try{
        // Process the Excel file and import the data
        set_time_limit(0); // This removes the time limit completely
        ini_set('memory_limit', '8G'); // Adjust the memory limit as needed


                $import = new UsersImport();
                $valaue=Excel::import($import, $this->filePath);
        if ($valaue) {
            // Update job status to completed
             ImportJobStatus::where('job_id', $this->jobId)->update([
                 'status' => 'completed',
                 'filename' => $this->filePath,
             ]);
         } else {
            ImportJobStatus::where('job_id', $this->jobId)->update(['status' => 'failed','filename' => $this->filePath]);
         }
        } catch (\Exception $e) {
            ImportJobStatus::where('job_id', $this->jobId)->update(['status' => 'failed','filename' => $this->filePath]);
            Log::error('Export job failed', ['error' => $e->getMessage()]);
        }
    }
}
