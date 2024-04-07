<?php

namespace App\Providers;

use App\Interfaces\BaseInterface;
use App\Interfaces\OtpInterface;
use App\Interfaces\UserInterface;
use App\Models\Otp;
use App\Models\User;
use App\Repositories\BaseRepository;
use App\Repositories\OtpRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\ServiceProvider;

class BackendServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(
            BaseInterface::class,
            BaseRepository::class
        );
        $this->app->bind(
            UserInterface::class,
            function() {
                return new UserRepository(new User);
            }
        );
        $this->app->bind(
            OtpInterface::class,
            function() {
                return new OtpRepository(new Otp);
            }
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
