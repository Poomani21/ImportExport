<?php

namespace App\Http\Controllers;

use App\Exports\UsersExport;
use App\Jobs\ExportUsersJob;
use App\Jobs\ImportUsersJob;
use App\Models\BulkUpload;
use App\Models\ExcelImportFiles;
use App\Models\ImportJobStatus;
use App\Models\JobStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function import(Request $request)
{
    
        // Validate the uploaded file
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:10240', // Optional: Limit file size to 10 MB
        ]);
    
        // Get the original file name and its extension
        $originalFileName = pathinfo($request->file('file')->getClientOriginalName(), PATHINFO_FILENAME);
        $fileExtension = $request->file('file')->getClientOriginalExtension();
    
        // Generate a unique file name with date and time
        $timestamp = now()->format('Y-m-d_H-i-s'); // Current date and time
        $newFileName = $originalFileName . '_' . $timestamp . '.' . $fileExtension;
    
        // Define the folder structure for storage
        $folderPath = 'upload/ExcelFiles';
    
        // Store the file in the public disk
        $filePath = $request->file('file')->storeAs(
            $folderPath,
            $newFileName,
            'public' // Specify the public disk
        );

        $jobId = (string) Str::uuid();
    
        // Create a JobStatus record to track the status of the job
        ImportJobStatus::create([
            'job_id' => $jobId,
            'status' => 'processing',
        ]);
    
        // Dispatch the job to process the file (optional)
        // ImportUsersJob::dispatch(public_path('storage/' . $filePath), $newFileName, $jobId);
        dispatch(new ImportUsersJob(public_path('storage/' . $filePath), $jobId));
        // Artisan::call('queue:listen --timeout=0 --memory=8192 --tries=5'); // This runs the queue worker once automatically

        // Store the file details in the database
        ExcelImportFiles::create([
            'fileName' => $newFileName,
            'filePath' => 'storage/' . $filePath, // Save the relative path for public URL access
        ]);
    
        return response()->json([
            'status' => 'processing',
            'message' => 'Your export is being processed. You will be notified once it is done.',
            'job_id' => $jobId, // Include the job_id in the response
        ]);
}
public function checkImportJobStatus($jobId)
{
    $status = ImportJobStatus::where('job_id', $jobId)->first();

if ($status && $status->status == 'completed') {
    return response()->json([
        'status' => 'completed',
        'file_path' => $status->filename,
    ]);
}

return response()->json(['status' => 'processing']);
}
    
    // List the imported files from the ExcelImportFiles table
    public function exportlist()
    {
        $files = ExcelImportFiles::all();  // Get all records from the 'excelfiles' table
        return view('export', compact('files'));
    }
    public function index(Request $request){
        // Get the search query from the request
    $search = $request->input('search');

    // Fetch users based on search or display all users if no search query
    $users = BulkUpload::when($search, function ($query) use ($search) {
        return $query->where('gender', 'like', "%$search%")
            ->orWhere('pincode', 'like', "%$search%")
            ->orWhere('city', 'like', "%$search%")
            ->orWhere('state', 'like', "%$search%")
            ->orWhere('country', 'like', "%$search%");
    })->paginate(200); // Paginate results


        // $users = BulkUpload::paginate(200); // Fetch paginated data with 10 users per page
        return view('user_list', compact('users','search'));
    }
    public function ExportFilterData(Request $request)
    {
        $search = $request->get('search');
    // Generate a unique job ID
    $jobId = (string) Str::uuid();

    // Create a JobStatus record to track the status of the job
    JobStatus::create([
        'job_id' => $jobId,
        'status' => 'processing',
    ]);

    dispatch(new ExportUsersJob($search, $jobId));
    // Artisan::call('queue:listen --timeout=0 --memory=8192 --tries=5'); // This runs the queue worker once automatically

    return response()->json([
        'status' => 'processing',
        'message' => 'Your export is being processed. You will be notified once it is done.',
        'job_id' => $jobId, // Include the job_id in the response

    ]);
    }

    public function checkJobStatus($jobId)
    {
        $status = JobStatus::where('job_id', $jobId)->first();

    if ($status && $status->status == 'completed') {
        return response()->json([
            'status' => 'completed',
            'file_path' => $status->filename,
        ]);
    }

    return response()->json(['status' => 'processing']);
    }
}
