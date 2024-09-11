<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\TagController;
use App\Http\Middleware\ApiAuthMiddleware;
use App\Http\Middleware\LanguageMiddleware;
use Illuminate\Support\Facades\Route;

Route::middleware([LanguageMiddleware::class])->group(function () {
    Route::get('/get_categories', [CategoryController::class, 'getCategories']);
    Route::get('/get_tags', [TagController::class, 'getTags']);


    Route::post('/signup', [AuthController::class, 'signup']);
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware([ApiAuthMiddleware::class])->group(function () {
        Route::get('/logout', [AuthController::class, 'logout']);
    });
});
