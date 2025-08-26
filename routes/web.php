<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});


// Socialite
Route::get('/auth/google', [App\Http\Controllers\GoogleAuthController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [App\Http\Controllers\GoogleAuthController::class, 'handleGoogleCallback'])->name('auth.google-callback');

// Menambahkan route untuk export (optional)
// routes/web.php
Route::middleware(['auth'])->group(function () {
    Route::get('/audit-logs/export', function () {
        return response()->download(storage_path('app/audit_logs_export.csv'));
    })->name('audit-logs.export');
});


// Route untuk sitemap (serve static file)
Route::get('/sitemap.xml', function () {
    $path = public_path('sitemap.xml');

    if (!file_exists($path)) {
        abort(404, 'Sitemap not found. Please generate sitemap first.');
    }

    return response()->file($path, [
        'Content-Type' => 'application/xml'
    ]);
})->name('sitemap');
