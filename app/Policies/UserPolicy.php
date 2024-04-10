<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    public function deleteOwnAccount(User $user): bool
    {
        return !$user->hasAnyRole(['admin', 'super-admin']);
    }
}
