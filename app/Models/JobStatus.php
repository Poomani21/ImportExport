<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class JobStatus extends Model
{
    use HasFactory;
    protected $fillable = [
        'job_id','status','created_at','updated_at','filename'

    ];
    protected $table='jobs_status';
}
