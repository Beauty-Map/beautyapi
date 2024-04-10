<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\IntroController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\UploadController;
use App\Http\Middleware\AdminMiddleware;
use Illuminate\Support\Facades\Route;

Route::get('/intros', [AuthController::class, 'register'])->middleware('guest');

Route::get('/me', [AuthController::class, 'me'])->middleware('auth:sanctum');
Route::post('/admin/login', [AuthController::class, 'adminLogin'])->middleware('guest');

Route::prefix('/auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register'])->middleware('guest');
    Route::post('/register/otp', [AuthController::class, 'checkOtpCode'])->middleware('guest');
    Route::post('/login', [AuthController::class, 'login'])->middleware('guest');
    Route::post('/password/forgot', [AuthController::class, 'forgotPassword'])->middleware('guest');
    Route::post('/password/otp', [AuthController::class, 'checkForgotPasswordOtpCode'])->middleware('guest');
    Route::post('/password', [AuthController::class, 'setPassword'])->middleware('guest');
});

Route::prefix('/admin')->middleware(['auth:sanctum', AdminMiddleware::class])->group(function () {
    Route::post('/upload', [UploadController::class, 'adminUpload']);
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
});
