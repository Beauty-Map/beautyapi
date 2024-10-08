<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ServiceResource extends JsonResource
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
            'image' => $this->image,
            'title' => $this->title,
            'is_active' => $this->is_active,
            'parent_id' => $this->parent_id,
            'parent' => $this->parent,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
