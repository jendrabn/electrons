<?php

use Illuminate\Support\Facades\Route;

Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');


// Socialite
Route::get('/auth/google', [App\Http\Controllers\AuthController::class, 'authWithGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [App\Http\Controllers\AuthController::class, 'authWithGoogleCallback'])->name('auth.google-callback');

// Menambahkan route untuk export (optional)
// routes/web.php
Route::middleware(['auth'])->group(function () {
    Route::get('/audit-logs/export', function () {
        return response()->download(storage_path('app/audit_logs_export.csv'));
    })->name('audit-logs.export');
});
