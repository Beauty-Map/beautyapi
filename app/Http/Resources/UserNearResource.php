<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserNearResource extends JsonResource
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
            'full_name' => $this->full_name,
            'portfolios_count' => $this->portfolios_count,
            'services_count' => $this->services_count,
            'address' => $this->address,
            'distance' => $this->distance,
            'rating' => $this->rating,
            'is_bookmarked' => $this->is_bookmarked,
            'avatar' => $this->avatar,
            'has_blue_tick' => $this->has_blue_tick,
            'created_at' => $this->created_at,
        ];
    }
}
