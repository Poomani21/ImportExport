<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ExcelImportFiles extends Model
{
    use HasFactory;
    protected $table='excelfiles';
    protected $fillable =['fileName','filePath','created_at','updated_at'];
}
