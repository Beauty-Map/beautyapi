<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BonusTransactionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'app' => $this->app,
            'status' => $this->status,
            'amount' => $this->amount,
            'user_id' => $this->user_id,
            'user' => new UserSimpleResource($this->user),
            'referrer_id' => $this->referrer_id,
            'referrer' => $this->referrer,
            'level' => $this->level,
        ];
    }
}
