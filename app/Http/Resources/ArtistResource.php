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
            'email' => $this->email,
            'bio' => $this->getBio(),
            'distance' => $this->distance,
            'is_bookmarked' => $this->is_bookmarked,
            'avatar' => $this->getAvatar(),
            'has_blue_tick' => $this->getHasBlueTick(),
            'banners' => $this->getArtistBanner(),
            'rating' => $this->getRating(),
            'address' => $this->getAddress(),
            'created_at' => $this->created_at,
            'portfolios_count' => $this->getPortfoliosCount(),
            'portfolios' => $this->portfolios,
            'services_count' => $this->getServicesCount(),
            'services' => $this->services,
            'work_hours' => $this->getWorkHours(),
            'licenses' => $this->getLicenses(),
            'location' => $this->getLocation(),
            'socials' => $this->getSocialMedia(),
            'view' => $this->getView(),
        ];
    }
}
