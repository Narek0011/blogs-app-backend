<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\BlogController;
use App\Http\Middleware\JwtMiddleware;
use Illuminate\Support\Facades\Route;

Route::post('signup', [AuthController::class, 'signup']);
Route::post('login', [AuthController::class, 'login']);

Route::middleware([JwtMiddleware::class])->group(function () {
    Route::get('getAuthUser', [AuthController::class, 'getAuthUser']);
    Route::get('logout', [AuthController::class, 'logout']);

    Route::prefix('blogs')->group(function () {
        Route::get('/', [BlogController::class, 'index']);
        Route::post('create', [BlogController::class, 'store']);
        Route::get('show/{blog}', [BlogController::class, 'show']);
        Route::delete('delete/{blog}', [BlogController::class, 'destroy']);
        Route::post('update/{blog}', [BlogController::class, 'update']);
    });
});

