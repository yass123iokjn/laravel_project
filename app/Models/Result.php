<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Result extends Model
{
    use HasFactory;

    protected $fillable = ['calcul_id', 'excel_file_id', 'result_data', 'calculated_at', 'reference'];

    public function calcul()
    {
        return $this->belongsTo(Calcul::class, 'calcul_id');
    }

    public function excelFile()
    {
        return $this->belongsTo(ExcelFile::class);
    }

    public function getResultDataAttribute($value)
    {
        return json_decode($value, true);
    }
}
