<?php

namespace App\Jobs;

use App\Events\ExportCompleted;
use App\Models\JobStatus;
use App\Exports\UsersExport;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Log;

class ExportUsersJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $search;
    protected $jobStatus;
    protected $jobId;

    /**
     * Create a new job instance.
     */
    public function __construct($search ,$jobId)
    {
        $this->search = $search;
        $this->jobId = $jobId;

    }

    /**
     * Execute the job.
     */
    public function handle()
    {
       
        try {
            Log::info('Search filter:', ['search' => $this->search]);

            ini_set('memory_limit', '8G'); // Adjust the memory limit as needed
            set_time_limit(0); // This removes the time limit completely

            $fileName = 'users_export_' . now()->format('Y-m-d_H-i-s') . '.xlsx';
            $filePath = 'upload/exports/' . $fileName;

            // Process export
            $value = Excel::store(new UsersExport($this->search), $filePath, 'public');

            if ($value) {
               // Update job status to completed
                JobStatus::where('job_id', $this->jobId)->update([
                    'status' => 'completed',
                    'filename' => $filePath,
                ]);
            } else {
                JobStatus::where('job_id', $this->jobId)->update(['status' => 'failed','filename' => $filePath]);
            }
        } catch (\Exception $e) {
            JobStatus::where('job_id', $this->jobId)->update(['status' => 'failed','filename' => $filePath]);
            Log::error('Export job failed', ['error' => $e->getMessage()]);
        }
    }

}
