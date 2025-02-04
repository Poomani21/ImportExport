<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BulkUpload extends Model
{
    use HasFactory;
    protected $fillable = [
        'name','gender','email','phone','pincode','description','city','state','country','created_at','updated_at'

    ];
    protected $table='bulk_upload';

}
