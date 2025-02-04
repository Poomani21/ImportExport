<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Request;

// Route::get('/', function () {
//     return view('welcome');
// });
Route::get('/', [UserController::class, 'exportlist'])->name('exportlist.list');
Route::post('/import-users', [UserController::class, 'import'])->name('import-users');
Route::get('/users', [UserController::class, 'index'])->name('user.index');
Route::get('/export-filter-data', [UserController::class, 'ExportFilterData'])->name('ExportFilterData');
Route::get('/check-job-status/{jobId}', [UserController::class, 'checkJobStatus'])->name('checkJobStatus');
Route::get('/check-import-job-status/{jobId}', [UserController::class, 'checkImportJobStatus'])->name('checkImportJobStatus');

