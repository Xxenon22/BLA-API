<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaterialType extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    // Relasi ke tabel files
    public function files()
    {
        return $this->hasMany(File::class, 'material_type_id');
    }
}
