<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\QuoteController;
use App\Http\Controllers\QuoteFolderController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Authentication
|--------------------------------------------------------------------------
*/
Route::get('/inchecken', [AuthController::class, 'showLogin'])->middleware('throttle:10,60')->name('login');
Route::post('/inchecken/reset-password/request', [PasswordResetController::class, 'request'])->middleware('throttle:3,60')->name('password.reset.request');
Route::get('/inchecken/reset-password/{token}', [PasswordResetController::class, 'show'])->name('password.reset.form');
Route::post('/inchecken/reset-password', [PasswordResetController::class, 'update'])->middleware('throttle:5,60')->name('password.reset.update');
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| Quotes (everything requires login)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::view('/', 'app', ['page' => 'Quotes', 'title' => 'Quotes'])->name('quotes');

    Route::prefix('api')->group(function () {
        // Quotes
        Route::get('/quotes', [QuoteController::class, 'index'])->name('api.quotes.index');
        Route::post('/quotes/reorder', [QuoteController::class, 'reorder'])->name('api.quotes.reorder');
        Route::post('/quotes', [QuoteController::class, 'store'])->name('api.quotes.store');
        Route::post('/quotes/{quote}', [QuoteController::class, 'update'])->name('api.quotes.update');
        Route::delete('/quotes/{quote}', [QuoteController::class, 'destroy'])->name('api.quotes.destroy');

        // Quote folders (reorder before {quoteFolder} so "reorder" isn't bound as a folder id)
        Route::post('/quote-folders/reorder', [QuoteFolderController::class, 'reorder'])->name('api.quote-folders.reorder');
        Route::post('/quote-folders', [QuoteFolderController::class, 'store'])->name('api.quote-folders.store');
        Route::post('/quote-folders/{quoteFolder}', [QuoteFolderController::class, 'update'])->name('api.quote-folders.update');
        Route::delete('/quote-folders/{quoteFolder}', [QuoteFolderController::class, 'destroy'])->name('api.quote-folders.destroy');
    });
});
