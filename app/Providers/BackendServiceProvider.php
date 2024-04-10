<?php

namespace App\Providers;

use App\Interfaces\BaseInterface;
use App\Interfaces\IntroInterface;
use App\Interfaces\MetaInterface;
use App\Interfaces\OtpInterface;
use App\Interfaces\PlanInterface;
use App\Interfaces\ServiceInterface;
use App\Interfaces\UserInterface;
use App\Models\Intro;
use App\Models\Meta;
use App\Models\Otp;
use App\Models\Plan;
use App\Models\Service;
use App\Models\User;
use App\Repositories\BaseRepository;
use App\Repositories\IntroRepository;
use App\Repositories\MetaRepository;
use App\Repositories\OtpRepository;
use App\Repositories\PlanRepository;
use App\Repositories\ServiceRepository;
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
        $this->app->bind(
            IntroInterface::class,
            function() {
                return new IntroRepository(new Intro);
            }
        );
        $this->app->bind(
            ServiceInterface::class,
            function() {
                return new ServiceRepository(new Service);
            }
        );
        $this->app->bind(
            PlanInterface::class,
            function() {
                return new PlanRepository(new Plan);
            }
        );
        $this->app->bind(
            MetaInterface::class,
            function() {
                return new MetaRepository(new Meta);
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
