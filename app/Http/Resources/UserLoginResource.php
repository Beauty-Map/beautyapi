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
            'tel_number' => $this->user->getTelNumber(),
            'coins' => $this->user->coins,
            'golds' => $this->user->getGolds(),
            'city_id' => $this->user->city_id,
            'city' => $this->user->city,
            'national_code' => $this->user->getNationalCode(),
            'address' => $this->user->getAddress(),
            'bio' => $this->user->getBio(),
            'location' => $this->user->getLocation(),
            'province_id' => $this->user->province_id,
            'province' => $this->user->getProvince(),
            'work_hours' => $this->user->getWorkHours(),
            'is_all_day_open' => $this->user->getIsAllDayOpen(),
            'work_on_holidays' => $this->user->getWorkOnHolidays(),
            'is_closed' => $this->user->getIsClosed(),
            'created_at' => $this->user->created_at,
            'token' => $this->token,
            'metas' => MetaResource::collection($this->metas),
            'permissions' => $this->getPermissionNames(),
            'roles' => $this->getRoleNames(),
            'avatar' => $this->getAvatar(),
            'birth_date' => $this->birth_date,
            'social_media' => $this->getSocialMedia(),
            'is_artist_profile_completed' => $this->getIsArtistProfileCompleted(),
        ];
    }
}
