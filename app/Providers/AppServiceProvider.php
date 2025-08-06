<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{

    public $singletons = [
        \Filament\Http\Responses\Auth\Contracts\LoginResponse::class => \App\Http\Responses\LoginResponse::class,
        \Filament\Http\Responses\Auth\Contracts\LogoutResponse::class => \App\Http\Responses\LogoutResponse::class,
    ];

    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrapFive();

        View::composer([
            'layouts.app',
            'home',
            'about',
            'contact',
            'posts.*',
        ], function ($view) {

            $categories = Cache::remember('categories', 1800, function () {
                return \App\Models\Category::withCount(['posts' => function ($query) {
                    $query->published();
                }])->get();
            });

            $tags = Cache::remember('tags', 1800, function () {
                return \App\Models\Tag::withCount(['posts' => function ($query) {
                    $query->published();
                }])->having('posts_count', '>', 0)->get();
            });

            $recentPosts = Cache::remember('recentPosts', 1800, function () {
                return \App\Models\Post::query()->recent()->get();
            });

            $popularPosts = Cache::remember('popularPosts', 1800, function () {
                return \App\Models\Post::query()->popular()->get();
            });

            $view->with(compact('categories', 'tags', 'recentPosts', 'popularPosts'));
        });
    }
}
