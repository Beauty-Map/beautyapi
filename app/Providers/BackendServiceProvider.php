<?php

namespace App\Providers;

use App\Interfaces\BaseInterface;
use App\Interfaces\IntroInterface;
use App\Interfaces\MetaInterface;
use App\Interfaces\OtpInterface;
use App\Interfaces\PaymentOptionInterface;
use App\Interfaces\PlanInterface;
use App\Interfaces\PortfolioInterface;
use App\Interfaces\ProvinceInterface;
use App\Interfaces\ServiceInterface;
use App\Interfaces\TicketInterface;
use App\Interfaces\TicketSubjectInterface;
use App\Interfaces\UserInterface;
use App\Interfaces\UserPlanInterface;
use App\Models\Intro;
use App\Models\Meta;
use App\Models\Otp;
use App\Models\PaymentOption;
use App\Models\Plan;
use App\Models\Portfolio;
use App\Models\Province;
use App\Models\Service;
use App\Models\Ticket;
use App\Models\TicketSubject;
use App\Models\User;
use App\Models\UserPlan;
use App\Repositories\BaseRepository;
use App\Repositories\IntroRepository;
use App\Repositories\MetaRepository;
use App\Repositories\OtpRepository;
use App\Repositories\PaymentOptionRepository;
use App\Repositories\PlanRepository;
use App\Repositories\PortfolioRepository;
use App\Repositories\ProvinceRepository;
use App\Repositories\ServiceRepository;
use App\Repositories\TicketRepository;
use App\Repositories\TicketSubjectRepository;
use App\Repositories\UserPlanRepository;
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
        $this->app->bind(
            PortfolioInterface::class,
            function() {
                return new PortfolioRepository(new Portfolio);
            }
        );
        $this->app->bind(
            UserPlanInterface::class,
            function() {
                return new UserPlanRepository(new UserPlan);
            }
        );
        $this->app->bind(
            ProvinceInterface::class,
            function() {
                return new ProvinceRepository(new Province);
            }
        );
        $this->app->bind(
            TicketSubjectInterface::class,
            function() {
                return new TicketSubjectRepository(new TicketSubject);
            }
        );
        $this->app->bind(
            TicketInterface::class,
            function() {
                return new TicketRepository(new Ticket);
            }
        );
        $this->app->bind(
            PaymentOptionInterface::class,
            function() {
                return new PaymentOptionRepository(new PaymentOption);
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
