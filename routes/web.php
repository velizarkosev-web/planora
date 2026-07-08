<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/products/{slug}', [ProductController::class, 'show'])->name('products.show');

// On-the-fly resized images (Glide). {path} allows slashes so it matches e.g.
// /img/products/cover.png?w=800&fm=webp
Route::get('/img/{path}', [ImageController::class, 'show'])
    ->where('path', '.*')
    ->name('image');
