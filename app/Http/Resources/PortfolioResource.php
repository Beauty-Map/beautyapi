<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PortfolioResource extends JsonResource
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
            'description' => $this->description,
            'price' => $this->price,
            'discount_price' => $this->discount_price,
            'has_tel' => $this->has_tel,
            'has_phone_number' => $this->has_phone_number,
            'showing_phone_number' => $this->showing_phone_number,
            'service' => $this->service_id,
            'service_id' => new ServiceSimpleResource($this->service),
            'user_id' => $this->user_id,
            'user' => new UserSimpleResource($this->user),
            'images' => $this->images_list,
            'work_hours' => $this->work_hours,
        ];
    }
}
