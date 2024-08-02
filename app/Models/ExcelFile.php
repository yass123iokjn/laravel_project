<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExcelFile extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'file_path', 'uploaded_at'];

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
