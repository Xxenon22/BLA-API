<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Folder extends Model
{
    use HasFactory;

    protected $table = 'folders';
    protected $fillable = [
        'folder_name',
        'description',
        'parent_id',
    ];

    public function parent()
    {
        return $this->belongsTo(Folder::class, 'parent_id');
    }
    public function files()
    {
        return $this->hasMany(File::class, 'folder_id');  // Pastikan 'folder_id' ada di tabel file
    }

    // Menambahkan relasi untuk subfolder
    public function children()
    {
        return $this->hasMany(Folder::class, 'parent_id');
    }
}
