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
            'maintenance' => $this->maintenance,
            'price' => $this->price,
            'discount_price' => $this->discount_price,
            'has_tel' => $this->has_tel,
            'has_phone_number' => $this->has_phone_number,
            'showing_phone_number' => $this->showing_phone_number,
            'service_id' => $this->service_id,
            'service' => new ServiceSimpleResource($this->service),
            'user_id' => $this->user_id,
            'user' => new UserSimpleResource($this->user),
            'images' => $this->images_list,
            'work_hours' => $this->work_hours,
            'is_bookmarked' => $this->is_bookmarked,
            'status' => $this->status,
            'getView' => $this->getView(),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
