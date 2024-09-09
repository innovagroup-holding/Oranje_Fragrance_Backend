<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\TagController;

// Route::get('/', function () {
//     return view('welcome');
// });
Route::get('/', [CategoryController::class, 'index']);


// category
Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
Route::post('/categories/store', [CategoryController::class, 'store'])->name('categories.store');
Route::get('/categories/create', [CategoryController::class, 'create'])->name('categories.create');
Route::put('/categories/{category}/update', [CategoryController::class, 'update'])->name('categories.update');
Route::delete('/categories/{category}/delete', [CategoryController::class, 'destroy'])->name('categories.delete');


//tag
Route::get('/tags', [TagController::class, 'index'])->name('tags.index');
Route::post('/tags/store', [TagController::class, 'store'])->name('tags.store');
Route::put('/tags/{tag}/update', [TagController::class, 'update'])->name('tags.update');
Route::delete('/tags/{tag}/delete', [TagController::class, 'destroy'])->name('tags.delete');