<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BannerController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SponsorController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\VideoController;
use App\Http\Middleware\ApiAuthMiddleware;
use App\Http\Middleware\LanguageMiddleware;
use Illuminate\Support\Facades\Route;

Route::middleware([LanguageMiddleware::class])->group(function () {

    Route::post('categories', [CategoryController::class, 'storeCategory']);
    Route::get('/get_categories', [CategoryController::class, 'getCategories']);
    Route::delete('categories', [CategoryController::class, 'destroy']);

    Route::post('tags', [TagController::class, 'storeTag']);
    Route::get('/get_tags', [TagController::class, 'getTags']);
    Route::delete('tags', [TagController::class, 'destroy']);


    Route::get('banners', [BannerController::class, 'getBanners']);
    Route::post('banners', [BannerController::class, 'storeBanner']);

    Route::post('cart/add', [OrderController::class, 'addToCart']);



    Route::get('products', [ProductController::class, 'getProducts']);
    Route::post('products', [ProductController::class, 'storeProduct']);
    Route::get('get_product', [ProductController::class, 'getProduct']);
    Route::post('update_product', [ProductController::class, 'updateProduct']);
    Route::delete('products', [ProductController::class, 'deleteProduct']);
    Route::post('products/add-tag', [ProductController::class, 'addTagToProduct']);
    Route::post('products/remove-tag', [ProductController::class, 'removeTagFromProduct']);
    Route::post('products/add-discount', [ProductController::class, 'addDiscountToProduct']);

    Route::get('get_user_data', [AuthController::class, 'getUser']);
    Route::post('/signup', [AuthController::class, 'signup']);
    Route::post('/login', [AuthController::class, 'login']);

    Route::get('videos', [VideoController::class, 'getVideos']);
    Route::post('videos', [VideoController::class, 'store']);

    Route::post('sponsors', [SponsorController::class, 'store']);
    Route::get('sponsors', [SponsorController::class, 'getSponsors']);

    Route::middleware([ApiAuthMiddleware::class])->group(function () {
        Route::get('/logout', [AuthController::class, 'logout']);
    });
});




    // Route::get('products', [ProductController::class, 'getAllProducts']);
    // Route::get('products/category/{categoryId}', [ProductController::class, 'getProductsByCategory']);
    // Route::get('products/tag/{tagId}', [ProductController::class, 'getProductsByTag']);
    // Route::get('products_by_category_tag', [ProductController::class, 'getProductsByCategoryAndTag']);
    // Route::get('hero_products', [ProductController::class, 'getHeroProducts']);