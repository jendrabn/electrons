<?php

namespace App\Providers;

use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{

    public $singletons = [
        \Filament\Auth\Http\Responses\LoginResponse::class => \App\Http\Responses\LoginResponse::class,
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
                return Category::withCount(['posts' => function ($query) {
                    $query->published();
                }])->get();
            });

            $tags = Cache::remember('tags', 1800, function () {
                return Tag::withCount(['posts' => function ($query) {
                    $query->published();
                }])->having('posts_count', '>', 0)->get();
            });

            $recentPosts = Cache::remember('recentPosts', 1800, function () {
                return Post::query()->recent()->get();
            });

            $popularPosts = Cache::remember('popularPosts', 1800, function () {
                return Post::query()->popular()->get();
            });

            $view->with(compact('categories', 'tags', 'recentPosts', 'popularPosts'));
        });
    }
}
