<?php

use App\Http\Controllers\Auth\SocialiteController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect('/app'));

Route::get('/auth/google', [SocialiteController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [SocialiteController::class, 'handleGoogleCallback'])->name('auth.google.callback');
