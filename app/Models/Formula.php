<?php

// app/Models/Formula.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Formula extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'expression', 'user_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function results()
    {
        return $this->hasMany(Result::class);
    }

    public function calculs()
    {
        return $this->hasMany(Calcul::class);
    }
}
