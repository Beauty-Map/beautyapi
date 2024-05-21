<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TicketResource extends JsonResource
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
            'title' => $this->title,
            'status' => $this->status,
            'description' => $this->description,
            'file' => $this->file,
            'is_published' => $this->is_published,
            'user_id' => $this->user_id,
            'user' => $this->user,
            'subject_id' => $this->subject_id,
            'subject' => $this->subject,
            'parent_id' => $this->parent_id,
            'parent' => $this->parent,
            'created_at' => $this->created_at,
        ];
    }
}
