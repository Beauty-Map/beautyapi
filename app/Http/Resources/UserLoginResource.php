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
            'full_name' => $this->user->full_name,
            'phone_number' => $this->user->phone_number,
            'city_id' => $this->user->city_id,
            'city' => $this->user->city,
            'province_id' => $this->user->province_id,
            'province' => $this->user->province,
            'created_at' => $this->user->created_at,
            'token' => $this->token,
            'metas' => MetaResource::collection($this->metas),
            'permissions' => $this->getPermissionNames(),
            'roles' => $this->getRoleNames(),
        ];
    }
}
