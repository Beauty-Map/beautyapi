<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserLoginResource extends JsonResource
{
    private User $user;
    private string $token;

    public function __construct(User $user, string $token)
    {
        $this->user = $user;
        $this->token = $token;
        parent::__construct($user);
    }

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->user->id,
            'email' => $this->user->email,
            'full_name' => $this->user->full_name,
            'phone_number' => $this->user->phone_number,
            'tel_number' => $this->user->tel_number,
            'coins' => $this->user->coins,
            'golds' => $this->user->golds,
            'city_id' => $this->user->city_id,
            'city' => $this->user->city,
            'national_code' => $this->user->national_code,
            'address' => $this->user->address,
            'bio' => $this->user->bio,
            'location' => $this->user->location,
            'province_id' => $this->user->province_id,
            'province' => $this->user->province,
            'work_hours' => $this->user->work_hours,
            'is_all_day_open' => $this->user->is_all_day_open,
            'work_on_holidays' => $this->user->work_on_holidays,
            'is_closed' => $this->user->is_closed,
            'created_at' => $this->user->created_at,
            'token' => $this->token,
            'metas' => MetaResource::collection($this->metas),
            'permissions' => $this->getPermissionNames(),
            'roles' => $this->getRoleNames(),
            'avatar' => $this->avatar,
            'birth_date' => $this->birth_date,
            'social_media' => $this->social_media,
            'is_artist_profile_completed' => $this->is_artist_profile_completed,
        ];
    }
}
