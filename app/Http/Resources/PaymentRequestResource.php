<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentRequestResource extends JsonResource
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
            'amount' => $this->amount,
            'user_id' => $this->user_id,
            'user' => new UserSimpleResource($this->user),
            'type' => $this->type,
            'status' => $this->status,
            'type_fa' => $this->type_fa,
            'status_fa' => $this->status_fa,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
