<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes (refactored)
|--------------------------------------------------------------------------
| Clean, commented routes file. Keep behavior identical to previous file
| but explicitly reference individual auth pages and avoid a missing
| unified 'auth.index' view.
*/

// Home
Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');


// Community (threads) routes
Route::prefix('comunity')->name('comunity.')->group(function () {
    // Public listing
    Route::get('/', [App\Http\Controllers\ThreadController::class, 'index'])->name('index');

    // Protected actions (create/store/edit/update) — register BEFORE the wildcard show
    // so static paths like '/create' are not captured by the wildcard show route.
    Route::middleware(['auth'])->group(function () {
        Route::get('/create', [App\Http\Controllers\ThreadController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\ThreadController::class, 'store'])->name('store');
        Route::get('/{thread}/edit', [App\Http\Controllers\ThreadController::class, 'edit'])->name('edit');
        Route::put('/{thread}', [App\Http\Controllers\ThreadController::class, 'update'])->name('update');
        Route::delete('/{thread}', [App\Http\Controllers\ThreadController::class, 'destroy'])->name('destroy');

        // Thread comments (use slug for thread parameter so URLs using slugs match the edit/show patterns)
        Route::post('/{thread}/comments', [App\Http\Controllers\ThreadCommentController::class, 'store'])->name('comments.store');
        Route::get('/{thread}/comments/{comment}/edit', [App\Http\Controllers\ThreadCommentController::class, 'edit'])->name('comments.edit');
        Route::put('/{thread}/comments/{comment}', [App\Http\Controllers\ThreadCommentController::class, 'update'])->name('comments.update');
        Route::delete('/{thread}/comments/{comment}', [App\Http\Controllers\ThreadCommentController::class, 'destroy'])->name('comments.destroy');
        Route::post('/{thread}/comments/{comment}/like', [App\Http\Controllers\ThreadCommentController::class, 'like'])->name('comments.like');

        // Thread like (toggle)
        Route::post('/{thread}/like', [App\Http\Controllers\ThreadController::class, 'like'])->name('like');
        // Thread bookmark toggle (owner can bookmark/unbookmark via ThreadController)
        Route::post('/{thread}/bookmark', [App\Http\Controllers\ThreadController::class, 'toggleBookmark'])->name('bookmark');
        Route::post('/{thread}/comments/{comment}/mark-best', [App\Http\Controllers\ThreadCommentController::class, 'markBest'])->name('comments.markBest');
        // Toggle thread answered state (only for thread owner)
        Route::post('/{thread}/toggle-done', [App\Http\Controllers\ThreadController::class, 'toggleDone'])->name('toggleDone');
    });

    // Suggest endpoint for search autocomplete
    Route::get('/suggest', [App\Http\Controllers\ThreadController::class, 'suggest'])->name('suggest');

    // Public show (wildcard) — keep this last so it doesn't match earlier static routes
    Route::get('/{thread}', [App\Http\Controllers\ThreadController::class, 'show'])->name('show');
});




// Provide a named 'login' route for compatibility with packages (Filament, etc.)
Route::get('/login', function () {
    return redirect()->route('auth.show.login');
})->middleware('guest')->name('login');

// Filament compatibility: accept logout POSTs from Filament panels and redirect to our auth login
Route::post('/filament/admin/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect()->route('auth.show.login');
})->middleware('auth')->name('compat.filament.admin.logout');

Route::post('/filament/author/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect()->route('auth.show.login');
})->middleware('auth')->name('compat.filament.author.logout');

Route::get('/users/{user}', function () {
    return 'user profile page';
})->name('users.show');



// Thread image uploads (publicly addressable route used by the editor)
Route::post('/threads/uploads', [App\Http\Controllers\ThreadController::class, 'uploadImage'])->name('threads.upload-image');

// Static pages
Route::get('/contact', fn() => view('contact'))->name('contact');
Route::get('/about', fn() => view('about'))->name('about');

// Posts routes (index, show, filters)
Route::get('/posts', [App\Http\Controllers\PostController::class, 'index'])->name('posts.index');
Route::get('/{post:slug}', [App\Http\Controllers\PostController::class, 'show'])->name('posts.show');
Route::get('/posts/category/{category:slug}', [App\Http\Controllers\PostController::class, 'category'])->name('posts.category');
Route::get('/posts/tag/{tag:slug}', [App\Http\Controllers\PostController::class, 'tag'])->name('posts.tag');
Route::get('/posts/author/{user:id}', [App\Http\Controllers\PostController::class, 'author'])->name('posts.author');

// Comments listing (AJAX) and authenticated actions
Route::get('/comments/{post}', [App\Http\Controllers\PostCommentController::class, 'list'])->name('comments.list');

Route::middleware(['auth'])->prefix('posts')->name('posts.')->group(function () {
    Route::post('/{post}/comments', [App\Http\Controllers\PostCommentController::class, 'store'])->name('comments.store');
    Route::get('/{post}/comments/{comment}/edit', [App\Http\Controllers\PostCommentController::class, 'edit'])->name('comments.edit');
    Route::put('/{post}/comments/{comment}', [App\Http\Controllers\PostCommentController::class, 'update'])->name('comments.update');
    Route::delete('/{post}/comments/{comment}', [App\Http\Controllers\PostCommentController::class, 'destroy'])->name('comments.destroy');
    Route::post('/{post}/comments/{comment}/like', [App\Http\Controllers\PostCommentController::class, 'like'])->name('comments.like');
});


// Custom auth routes (explicit, well-named)
Route::prefix('auth')->name('auth.')->group(function () {
    // Routes available only to guests
    Route::middleware('guest')->group(function () {
        // Show individual auth pages
        Route::get('/login', [App\Http\Controllers\AuthController::class, 'showLogin'])->name('show.login');
        Route::get('/register', [App\Http\Controllers\AuthController::class, 'showRegister'])->name('show.register');
        Route::get('/forgot', [App\Http\Controllers\AuthController::class, 'showForgot'])->name('forgot');
        Route::get('/reset/{token}', [App\Http\Controllers\AuthController::class, 'showReset'])->name('reset.form');

        // Form submissions
        Route::post('/login', [App\Http\Controllers\AuthController::class, 'login'])->name('login');
        Route::post('/register', [App\Http\Controllers\AuthController::class, 'register'])->name('register');
        Route::post('/send-reset', [App\Http\Controllers\AuthController::class, 'sendResetLinkEmail'])->name('send-reset');
        Route::post('/reset', [App\Http\Controllers\AuthController::class, 'reset'])->name('reset');

        // Social login (Google)
        Route::get('/google', [App\Http\Controllers\AuthController::class, 'redirectToGoogle'])->name('google');
        Route::get('/google/callback', [App\Http\Controllers\AuthController::class, 'handleGoogleCallback'])->name('google-callback');
    });

    // Logout only for authenticated users
    Route::post('/logout', [App\Http\Controllers\AuthController::class, 'logout'])->middleware('auth')->name('logout');
});

// Optional routes: export and sitemap
Route::middleware(['auth'])->group(function () {
    Route::get('/audit-logs/export', function () {
        return response()->download(storage_path('app/audit_logs_export.csv'));
    })->name('audit-logs.export');
});

$serveSitemap = function () {
    $path = public_path('sitemaps/sitemap.xml');

    if (!file_exists($path)) {
        abort(404, 'Sitemap not found. Please generate sitemap first.');
    }

    return response()->file($path, [
        'Content-Type' => 'application/xml'
    ]);
};

// Primary sitemap URL (index inside /sitemaps)
Route::get('/sitemaps/sitemap.xml', $serveSitemap)->name('sitemap');

// Backwards-compatible top-level path used by some crawlers/hosts
Route::get('/sitemap.xml', $serveSitemap);
