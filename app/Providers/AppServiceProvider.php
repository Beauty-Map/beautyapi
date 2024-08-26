<?php

namespace App\Providers;

use App\Models\Portfolio;
use App\Models\Service;
use App\Models\User;
use App\Models\Wallet;
use App\Policies\PortfolioPolicy;
use App\Policies\UserPolicy;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Laravel\Passport\Passport;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Relation::morphMap([
            'user' => User::class,
            'portfolio' => Portfolio::class,
            'service' => Service::class,
            'wallet' => Wallet::class,
        ]);

        Gate::define('delete-own-account', [UserPolicy::class, 'deleteOwnAccount']);
        Gate::define('update-portfolio', [PortfolioPolicy::class, 'updatePortfolio']);
        Gate::define('show-portfolio', [PortfolioPolicy::class, 'showPortfolio']);
        Gate::define('delete-portfolio', [PortfolioPolicy::class, 'deletePortfolio']);
        Gate::define('store-portfolio', [PortfolioPolicy::class, 'storePortfolio']);

//        Passport::loadKeysFrom(__DIR__.'/../secrets/oauth');
    }
}
