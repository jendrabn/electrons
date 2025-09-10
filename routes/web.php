<?php

use Filament\Http\Middleware\Authenticate;
use Illuminate\Support\Facades\Route;

Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/contact', fn() => view('contact'))->name('contact');
Route::get('/about', fn() => view('about'))->name('about');
Route::get('/posts', [App\Http\Controllers\PostController::class, 'index'])->name('posts.index');
Route::get('/{post:slug}', [App\Http\Controllers\PostController::class, 'show'])->name('posts.show');
Route::get('/posts/category/{category:slug}', [App\Http\Controllers\PostController::class, 'category'])->name('posts.category');
Route::get('/posts/tag/{tag:slug}', [App\Http\Controllers\PostController::class, 'tag'])->name('posts.tag');
Route::get('/posts/author/{user:id}', [App\Http\Controllers\PostController::class, 'author'])->name('posts.author');

Route::get('/comments/{post}', [App\Http\Controllers\PostCommentController::class, 'list'])->name('comments.list');

Route::middleware([Authenticate::class])->group(function () {
    Route::post('/comments', [App\Http\Controllers\PostCommentController::class, 'store'])->name('comments.store');
    Route::put('/comments/{comment}', [App\Http\Controllers\PostCommentController::class, 'update'])->name('comments.update');
    Route::post('/comments/{comment}/reply', [App\Http\Controllers\PostCommentController::class, 'reply'])->name('comments.reply');
    Route::post('/comments/{comment}/like', [App\Http\Controllers\PostCommentController::class, 'like'])->name('comments.like');
    Route::delete('/comments/{comment}', [App\Http\Controllers\PostCommentController::class, 'destroy'])->name('comments.destroy');
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
Route::get('/sitemaps/sitemap.xml', function () {
    $path = public_path('sitemap.xml');

    if (!file_exists($path)) {
        abort(404, 'Sitemap not found. Please generate sitemap first.');
    }

    return response()->file($path, [
        'Content-Type' => 'application/xml'
    ]);
})->name('sitemap');
