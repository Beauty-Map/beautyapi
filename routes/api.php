<?php

use App\Helpers\Helper;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BonusTransactionController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\IntroController;
use App\Http\Controllers\MainSliderController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PaymentOptionController;
use App\Http\Controllers\PaymentRequestController;
use App\Http\Controllers\PlanController;
use App\Http\Controllers\PortfolioController;
use App\Http\Controllers\ProvinceController;
use App\Http\Controllers\ReferralController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\TicketSubjectController;
use App\Http\Controllers\UploadController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserPlanController;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\CheckMicroKey;
use App\Mail\SendRegisterVerifyCodeEmail;
use App\Models\Otp;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;


use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

Route::get('/intros', [IntroController::class, 'index']);
Route::get('/provinces', [ProvinceController::class, 'index']);
Route::get('/portfolios', [PortfolioController::class, 'index']);
Route::get('/portfolios/{id}', [PortfolioController::class, 'show']);
Route::post('/portfolios/{id}/view', [PortfolioController::class, 'addView']);
Route::post('/portfolios/{id}/like', [PortfolioController::class, 'like'])->middleware('auth:api');
Route::post('/artists/{id}/like', [UserController::class, 'like'])->middleware('auth:api');
Route::get('/subjects', [TicketSubjectController::class, 'index']);
Route::get('/nearest', [UserController::class, 'nearest']);
Route::post('/ladder', [UserController::class, 'doLadder'])->middleware('auth:api');
Route::get('/applications', [ApplicationController::class, 'index']);

Route::prefix('/users')->group(function () {
    Route::get('/{id}', [UserController::class, 'show']);
    Route::post('/{id}/view', [UserController::class, 'addView']);
    Route::get('/{id}/portfolios', [PortfolioController::class, 'userIndex']);
});
Route::prefix('/services')->group(function () {
    Route::get('/', [ServiceController::class, 'index']);
    Route::get('/children', [ServiceController::class, 'indexChildren']);
    Route::get('/{id}', [ServiceController::class, 'show']);
    Route::get('/{id}/children', [ServiceController::class, 'children']);
});

Route::get('/main-sliders', [MainSliderController::class, 'index']);
Route::get('/search', [SearchController::class, 'search']);
Route::get('/search/artists', [SearchController::class, 'searchArtists']);

Route::post('/upload', [UploadController::class, 'upload'])->middleware('auth:api');

Route::prefix('/plans')->middleware('auth:api')->group(function () {
    Route::get('/', [PlanController::class, 'indexBuyable']);
});
Route::prefix('/payment-options')->middleware('auth:api')->group(function () {
    Route::get('/', [PaymentOptionController::class, 'index']);
    Route::get('/{id}', [PaymentOptionController::class, 'show']);
});
Route::prefix('/payments')->group(function () {
    Route::post('/', [PaymentController::class, 'store'])->middleware('auth:api');
    Route::get('/{code}', [PaymentController::class, 'show']);
    Route::post('/verify', [PaymentController::class, 'verify'])->middleware(CheckMicroKey::class);
    Route::post('/status', [PaymentController::class, 'status'])->middleware('auth:api');
})->middleware('auth:api');
Route::prefix('/own')->middleware('auth:api')->group(function () {
    Route::get('/', [AuthController::class, 'own']);
    Route::put('/', [UserController::class, 'updateProfile']);
    Route::get('/plan', [UserController::class, 'getOwnPlan']);
    Route::put('/wallet', [UserController::class, 'setWalletAddress']);
    Route::get('/statistics', [AuthController::class, 'statistics']);
    Route::get('/statistics/details', [AuthController::class, 'statisticsDetails']);
    Route::get('/notifications', [NotificationController::class, 'indexNotifications']);
    Route::get('/notifications/unread', [NotificationController::class, 'indexUnreadNotifications']);
    Route::get('/artist/notifications', [NotificationController::class, 'indexArtistNotifications']);
    Route::get('/artist/notifications/unread', [NotificationController::class, 'indexArtistUnreadNotifications']);
    Route::put('/password', [UserController::class, 'updatePassword']);
    Route::post('/alt-email', [UserController::class, 'sendOtpForAltNumber']);
    Route::delete('/', [UserController::class, 'deleteAccount']);
    Route::put('/artist', [UserController::class, 'updateArtistProfile']);
    Route::put('/artist/agreement', [UserController::class, 'doArtistAgreement']);
    Route::get('/artists/favourite', [UserController::class, 'indexFavouriteArtists']);
    Route::prefix('/portfolios')->group(function () {
        Route::get('/', [PortfolioController::class, 'ownIndex']);
        Route::post('/', [PortfolioController::class, 'store']);
        Route::get('/laddering', [PortfolioController::class, 'indexLaddering']);
        Route::get('/favourite', [PortfolioController::class, 'indexFavouritePortfolios']);
        Route::get('/{id}', [PortfolioController::class, 'show']);
        Route::put('/{id}', [PortfolioController::class, 'update']);
        Route::delete('/{id}', [PortfolioController::class, 'destroy']);
    });
    Route::prefix('/bonuses')->group(function () {
        Route::get('/', [BonusTransactionController::class, 'ownIndex']);
    });
    Route::get('/incomes', [BonusTransactionController::class, 'ownIncome']);
    Route::get('/refers', [UserController::class, 'refers']);
    Route::prefix('/tickets')->group(function () {
        Route::get('/', [TicketController::class, 'index']);
        Route::post('/', [TicketController::class, 'store']);
        Route::get('/{id}', [TicketController::class, 'show']);
        Route::put('/{id}', [TicketController::class, 'close']);
    });
    Route::prefix('/plans')->group(function () {
        Route::get('/', [UserPlanController::class, 'index']);
        Route::post('/', [UserPlanController::class, 'selectPlan']);
        Route::get('/{id}', [UserPlanController::class, 'show']);
    });
    Route::prefix('/payments')->group(function () {
        Route::prefix('/requests')->group(function () {
            Route::get('/', [PaymentRequestController::class, 'indexOwn']);
            Route::post('/', [PaymentRequestController::class, 'store']);
            Route::get('/{id}', [PaymentRequestController::class, 'show']);
            Route::delete('/{id}', [PaymentRequestController::class, 'destroy']);
        });
    });
    Route::prefix('/referrals')->group(function () {
        Route::get('/', [UserController::class, 'indexBestReferrals']);
    });
});
Route::prefix('/subscriptions')->group(function () {
    Route::get('/', [SubscriptionController::class, 'index'])->middleware('auth:api');
    Route::get('/{subscription}', [AdminController::class, 'showSubscription'])->middleware('auth:api');
});
Route::prefix('/courses')->group(function () {
    Route::get('/', [CourseController::class, 'index']);
    Route::get('/{course}', [CourseController::class, 'show']);
});

Route::prefix('/auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register'])->middleware('guest');
    Route::post('/register/otp', [AuthController::class, 'checkOtpCode'])->middleware('guest');
    Route::post('/login', [AuthController::class, 'login'])->middleware('guest');
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:api');
    Route::post('/password/forgot', [AuthController::class, 'forgotPassword'])->middleware('guest');
    Route::post('/password/otp', [AuthController::class, 'checkForgotPasswordOtpCode'])->middleware('guest');
    Route::post('/password', [AuthController::class, 'setPassword'])->middleware('guest');
});

Route::post('/admin/login', [AuthController::class, 'adminLogin'])->middleware('guest');
Route::prefix('/admin')->middleware(['auth:api', AdminMiddleware::class])->group(function () {
    Route::get('/sliders', [MainSliderController::class, 'index']);
    Route::put('/sliders/{slider}', [MainSliderController::class, 'update']);

    Route::prefix('/notifications')->group(function () {
        Route::get('/', [AdminController::class, 'indexNotifications']);
        Route::post('/', [AdminController::class, 'storeNotification'])->middleware('auth:api');
        Route::get('/{notification}', [AdminController::class, 'showNotification']);
        Route::delete('/{notification}', [AdminController::class, 'destroyNotification'])->middleware('auth:api');
    });

    Route::prefix('/courses')->group(function () {
        Route::get('/', [CourseController::class, 'index']);
        Route::post('/', [CourseController::class, 'store'])->middleware('auth:api');
        Route::get('/{course}', [CourseController::class, 'show']);
        Route::put('/{course}', [CourseController::class, 'update'])->middleware('auth:api');
        Route::delete('/{course}', [CourseController::class, 'destroy'])->middleware('auth:api');
    });

    Route::prefix('/provinces')->group(function () {
        Route::get('/', [ProvinceController::class, 'index']);
        Route::post('/', [ProvinceController::class, 'store'])->middleware('auth:api');
        Route::get('/{province}', [ProvinceController::class, 'show']);
        Route::put('/{province}', [ProvinceController::class, 'update'])->middleware('auth:api');
        Route::delete('/{province}', [ProvinceController::class, 'destroy'])->middleware('auth:api');
    });
    Route::prefix('/subscriptions')->group(function () {
        Route::get('/', [AdminController::class, 'indexSubscriptions'])->middleware('auth:api');
        Route::post('/', [AdminController::class, 'storeSubscription'])->middleware('auth:api');
        Route::get('/{subscription}', [AdminController::class, 'showSubscription'])->middleware('auth:api');
        Route::put('/{subscription}', [AdminController::class, 'updateSubscription'])->middleware('auth:api');
        Route::delete('/{subscription}', [AdminController::class, 'destroySubscription'])->middleware('auth:api');
    });
    Route::get('/own', [AuthController::class, 'own']);
    Route::get('/roles', [AdminController::class, 'indexRoles']);
    Route::prefix('/users')->group(function () {
        Route::get('/', [AdminController::class, 'indexUsers']);
        Route::get('/{id}', [AdminController::class, 'showUser']);
        Route::put('/{id}', [AdminController::class, 'updateUser']);
        Route::delete('/{id}', [AdminController::class, 'destroyUser']);

        Route::prefix('/{id}/bonuses')->group(function () {
            Route::get('/', [BonusTransactionController::class, 'userIndex']);
        });

        Route::prefix('/{id}/portfolios')->group(function () {
            Route::get('/', [AdminController::class, 'indexUserPortfolios']);
            Route::get('/{portfolio}', [AdminController::class, 'showUserPortfolio']);
            Route::put('/{portfolio}', [AdminController::class, 'updateUserPortfolio']);
            Route::patch('/{portfolio}', [AdminController::class, 'updateUserPortfolioStatus']);
            Route::delete('/{portfolio}', [AdminController::class, 'destroyUserPortfolio']);
        });
    });

    Route::prefix('/portfolios')->group(function () {
        Route::get('/', [AdminController::class, 'indexPortfolios']);
        Route::get('/{portfolio}', [AdminController::class, 'showPortfolio']);
        Route::put('/{portfolio}', [AdminController::class, 'updatePortfolio']);
        Route::patch('/{portfolio}', [AdminController::class, 'updatePortfolioStatus']);
        Route::delete('/{portfolio}', [AdminController::class, 'destroyPortfolio']);
    });

    Route::prefix('/requests')->group(function () {
        Route::get('/', [AdminController::class, 'indexPaymentRequests']);
        Route::get('/{id}', [AdminController::class, 'showPaymentRequest']);
        Route::put('/{id}', [AdminController::class, 'updatePaymentRequest']);
        Route::patch('/{id}', [AdminController::class, 'updatePaymentRequestStatus']);
    });
    Route::prefix('/intros')->group(function () {
        Route::get('/', [IntroController::class, 'index']);
        Route::post('/', [IntroController::class, 'store']);
        Route::get('/{id}', [IntroController::class, 'show']);
        Route::put('/{id}', [IntroController::class, 'update']);
        Route::delete('/{id}', [IntroController::class, 'destroy']);
    });

    Route::prefix('/applications')->group(function () {
        Route::get('/', [ApplicationController::class, 'index']);
        Route::post('/', [ApplicationController::class, 'store']);
        Route::get('/{id}', [ApplicationController::class, 'show']);
        Route::put('/{id}', [ApplicationController::class, 'update']);
        Route::delete('/{id}', [ApplicationController::class, 'destroy']);
    });

    Route::prefix('/services')->group(function () {
        Route::get('/', [ServiceController::class, 'adminIndex']);
        Route::post('/', [ServiceController::class, 'store']);
        Route::get('/{id}', [ServiceController::class, 'show']);
        Route::put('/{id}', [ServiceController::class, 'update']);
        Route::delete('/{id}', [ServiceController::class, 'destroy']);
    });
    Route::prefix('/plans')->group(function () {
        Route::get('/', [PlanController::class, 'index']);
        Route::get('/{id}', [PlanController::class, 'show']);
        Route::put('/{id}', [PlanController::class, 'update']);
    });
    Route::prefix('/tickets')->group(function () {
        Route::get('/', [AdminController::class, 'indexTickets']);
        Route::post('/', [AdminController::class, 'storeTicket']);
        Route::post('/{id}/answer', [AdminController::class, 'storeTicketAnswer']);
        Route::get('/{id}', [AdminController::class, 'showTicket']);
        Route::put('/{id}', [AdminController::class, 'closeTicket']);
    });
    Route::prefix('/subjects')->group(function () {
        Route::get('/', [TicketSubjectController::class, 'index']);
        Route::post('/', [TicketSubjectController::class, 'store']);
        Route::get('/{id}', [TicketSubjectController::class, 'show']);
        Route::put('/{id}', [TicketSubjectController::class, 'update']);
        Route::delete('/{id}', [TicketSubjectController::class, 'destroy']);
    });
    Route::prefix('/payment-options')->group(function () {
        Route::get('/', [PaymentOptionController::class, 'index']);
        Route::post('/', [PaymentOptionController::class, 'store']);
        Route::get('/{id}', [PaymentOptionController::class, 'show']);
        Route::put('/{id}', [PaymentOptionController::class, 'update']);
        Route::delete('/{id}', [PaymentOptionController::class, 'destroy']);
    });
});

Route::get('/referrals', [ReferralController::class, 'index']);
Route::get('/settings', [SettingController::class, 'index'])->middleware('auth:api');
Route::put('/settings', [SettingController::class, 'update'])->middleware('auth:api');
Route::put('/rule', [SettingController::class, 'updateRule'])->middleware('auth:api');
Route::put('/help', [SettingController::class, 'updateHelp'])->middleware('auth:api');
