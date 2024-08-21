<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ArtistResource extends JsonResource
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
            'phone_number' => $this->phone_number,
            'bio' => $this->bio,
            'distance' => $this->distance,
            'is_bookmarked' => $this->is_bookmarked,
            'avatar' => $this->avatar,
            'has_blue_tick' => $this->has_blue_tick,
            'banners' => $this->artist_banner,
            'rating' => $this->rating,
            'address' => $this->address,
            'created_at' => $this->created_at,
            'portfolios_count' => $this->portfolios_count,
            'portfolios' => $this->portfolios,
            'services_count' => $this->services_count,
            'services' => $this->services,
            'work_hours' => $this->work_hours,
            'licenses' => $this->licenses,
            'location' => $this->location,
        ];
    }
}
