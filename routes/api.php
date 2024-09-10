<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\TagController;

use Illuminate\Support\Facades\Route;

Route::middleware([\App\Http\Middleware\LanguageMiddleware::class])->group(function () {
    Route::get('/get_categories', [CategoryController::class, 'getCategories']);

    Route::get('/get_tags', [TagController::class, 'getTags']);
});