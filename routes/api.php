<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\IntroController;
use App\Http\Controllers\PlanController;
use App\Http\Controllers\PortfolioController;
use App\Http\Controllers\ProvinceController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\TicketSubjectController;
use App\Http\Controllers\UploadController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserPlanController;
use App\Http\Middleware\AdminMiddleware;
use Illuminate\Support\Facades\Route;

Route::get('/intros', [IntroController::class, 'index']);
Route::get('/provinces', [ProvinceController::class, 'index']);
Route::get('/portfolios', [PortfolioController::class, 'index']);
Route::get('/subjects', [TicketSubjectController::class, 'index']);

Route::prefix('/services')->group(function () {
    Route::get('/', [ServiceController::class, 'index']);
    Route::get('/{id}', [ServiceController::class, 'show']);
});

Route::get('/search', [SearchController::class, 'search']);

Route::post('/admin/login', [AuthController::class, 'adminLogin'])->middleware('guest');
Route::post('/upload', [UploadController::class, 'upload'])->middleware('auth:sanctum');

Route::prefix('/own')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [AuthController::class, 'own']);
    Route::put('/', [UserController::class, 'updateProfile']);
    Route::put('/password', [UserController::class, 'updatePassword']);
    Route::post('/alt-number', [UserController::class, 'sendOtpForAltNumber']);
    Route::put('/alt-number', [UserController::class, 'updateAltNumber']);
    Route::delete('/', [UserController::class, 'deleteAccount']);
    Route::prefix('/portfolios')->group(function () {
        Route::get('/', [PortfolioController::class, 'ownIndex']);
        Route::post('/', [PortfolioController::class, 'store']);
        Route::get('/{id}', [PortfolioController::class, 'show']);
        Route::put('/{id}', [PortfolioController::class, 'update']);
        Route::delete('/{id}', [PortfolioController::class, 'destroy']);
    });
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
});

Route::prefix('/auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register'])->middleware('guest');
    Route::post('/register/otp', [AuthController::class, 'checkOtpCode'])->middleware('guest');
    Route::post('/login', [AuthController::class, 'login'])->middleware('guest');
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
    Route::post('/password/forgot', [AuthController::class, 'forgotPassword'])->middleware('guest');
    Route::post('/password/otp', [AuthController::class, 'checkForgotPasswordOtpCode'])->middleware('guest');
    Route::post('/password', [AuthController::class, 'setPassword'])->middleware('guest');
});

Route::prefix('/admin')->middleware(['auth:sanctum', AdminMiddleware::class])->group(function () {
    Route::prefix('/intros')->group(function () {
        Route::get('/', [IntroController::class, 'index']);
        Route::post('/', [IntroController::class, 'store']);
        Route::get('/{id}', [IntroController::class, 'show']);
        Route::put('/{id}', [IntroController::class, 'update']);
        Route::delete('/{id}', [IntroController::class, 'destroy']);
    });
    Route::prefix('/services')->group(function () {
        Route::get('/', [ServiceController::class, 'index']);
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
    Route::prefix('/subjects')->group(function () {
        Route::get('/', [TicketSubjectController::class, 'index']);
        Route::post('/', [TicketSubjectController::class, 'store']);
        Route::get('/{id}', [TicketSubjectController::class, 'show']);
        Route::put('/{id}', [TicketSubjectController::class, 'update']);
        Route::delete('/{id}', [TicketSubjectController::class, 'destroy']);
    });
});

