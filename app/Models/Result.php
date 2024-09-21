<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Result extends Model
{
    use HasFactory;

    protected $fillable = ['calcul_id', 'excel_file_id', 'result_data', 'calculated_at', 'reference', 'formula_id'];

    // Relation avec le modèle Calcul
    public function calcul()
    {
        return $this->belongsTo(Calcul::class, 'calcul_id');
    }

    // Relation avec le modèle ExcelFile
    public function excelFile()
    {
        return $this->belongsTo(ExcelFile::class);
    }

    // Nouvelle relation avec le modèle Formula
    public function formula()
    {
        return $this->belongsTo(Formula::class, 'formula_id');
    }

    public function getResultDataAttribute($value)
    {
        return json_decode($value, true);
    }
}