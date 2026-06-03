<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    // Renders resources/js/Pages/Welcome.vue, passing `appName` as a prop.
    return Inertia::render('Welcome', [
        'appName' => config('app.name'),
    ]);
});
