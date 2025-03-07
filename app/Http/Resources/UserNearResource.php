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
            'portfolios_count' => $this->getPortfoliosCount(),
            'services_count' => $this->getServicesCount(),
            'services' => $this->services,
            'address' => $this->getAddress(),
            'distance' => $this->distance,
            'rating' => $this->getRating(),
            'is_bookmarked' => $this->isBookmarked(),
            'avatar' => $this->getAvatar(),
            'has_blue_tick' => $this->getHasBlueTick(),
            'created_at' => $this->created_at,
            'plan' => $this->getSelectedPlan(),
        ];
    }
}
