<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\TagController;

use Illuminate\Support\Facades\Route;

Route::get('/get_categories', [CategoryController::class, 'getCategories']);

Route::get('/get_tags', [TagController::class, 'getTags']);