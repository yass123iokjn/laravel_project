<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Calcul extends Model
{
    use HasFactory;

    protected $fillable = ['nom_calcul', 'reference', 'excel_file_id', 'formula_id', 'result_id'];

    public function excelFile()
    {
        return $this->belongsTo(ExcelFile::class);
    }

    public function formula()
    {
        return $this->belongsTo(Formula::class);
    }

    public function results()
{
    return $this->hasMany(Result::class, 'calcul_id');
}

}
