<?php

namespace App\Policies;

use App\Models\Portfolio;
use App\Models\User;

class PortfolioPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    public function store(User $user)
    {
        return $user->hasRole('artist');
    }

    public function updatePortfolio(User $user, Portfolio $portfolio)
    {
        return $user->hasRole('artist') && $user->id == $portfolio->user_id;
    }

    public function showPortfolio(User $user, Portfolio $portfolio)
    {
        return $user->hasRole('artist') && $user->id == $portfolio->user_id;
    }

    public function deletePortfolio(User $user, Portfolio $portfolio)
    {
        return $user->hasRole('artist') && $user->id == $portfolio->user_id;
    }
}
