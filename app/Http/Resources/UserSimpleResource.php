<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserSimpleResource extends JsonResource
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
            'rating' => $this->getRating(),
            'coins' => $this->getCoins(),
            'portfolios_count' => $this->getPortfoliosCount(),
            'subscription' => $this->activeSubscription(),
            'referrer_code' => $this->referrer_code,
            'plan' => $this->getSelectedPlan(),
            'avatar' => $this->getAvatar(),
            'ton_wallet_address' => $this->getTonWalletAddress(),
        ];
    }
}
