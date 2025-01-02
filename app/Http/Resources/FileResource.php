<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'folder_id' => $this->folder_id,
            'type_id' => $this->type_id,  // Menyertakan type_id dari file
            'product_name' => $this->product_name,
            'contact_person' => $this->contact_person,
            'vendor' => $this->vendor,
            'website' => $this->website,
            'material_position' => $this->material_position,
            'material_description' => $this->material_description,
            'image' => $this->image,
            'material_type' => $this->materialType ? [
                'id' => $this->materialType->id,
                'name' => $this->materialType->name,
            ] : null,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
