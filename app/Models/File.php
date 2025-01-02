<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    use HasFactory;

    protected $fillable = [
        'type_id',
        'product_name',
        'contact_person',
        'vendor',
        'material_position',
        'material_description',
        'website',
        'image',
        'folder_id',
        'material_type_id',
    ];

    // Relasi ke tabel material_types
    public function materialType()
    {
        return $this->belongsTo(MaterialType::class, 'material_type_id');
    }

    public function subfolder()
    {
        return $this->belongsTo(Folder::class, 'subfolder_id');
    }

    public function folder()
    {
        return $this->belongsTo(Folder::class);
    }
}
