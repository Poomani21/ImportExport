<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImportJobStatus extends Model
{
    Use HasFactory;
    protected $table='import_jobs_status';
    protected $fillable =['job_id','filename','status'];

}
