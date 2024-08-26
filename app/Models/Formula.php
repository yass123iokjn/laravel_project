<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Formula extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'expression', 'user_id'];

    // Relation avec le modèle User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relation avec le modèle Result
    public function results()
    {
        return $this->hasMany(Result::class, 'formula_id');
    }

    // Relation avec le modèle Calcul
    public function calculs()
    {
        return $this->hasMany(Calcul::class);
    }
}
