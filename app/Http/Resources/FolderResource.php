<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FolderResource extends JsonResource
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
            'folder_name' => $this->folder_name,
            'description' => $this->description,
            'parent_id' => $this->parent_id,
            'children' => FolderResource::collection($this->whenLoaded('children')),
            'files' => FileResource::collection($this->whenLoaded('files')), // Menggunakan FileResource untuk file
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
