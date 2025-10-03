<?php

namespace App\Providers;

use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Carbon;
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
            'layouts.*',
            'frontpages.*',
            'partials.*'
        ], function ($view) {

            $ttl = now()->addMinutes(30);

            // Categories
            $categories = Cache::get('categories');
            if (is_null($categories)) {
                $categories = Category::withCount(['posts' => function ($query) {
                    $query->published();
                }])->get();

                if ($categories->isNotEmpty()) {
                    Cache::put('categories', $categories, $ttl);
                }
            }

            // Tags
            $tags = Cache::get('tags');
            if (is_null($tags)) {
                $tags = Tag::withCount(['posts' => function ($query) {
                    $query->published();
                }])->having('posts_count', '>', 0)->get();

                if ($tags->isNotEmpty()) {
                    Cache::put('tags', $tags, $ttl);
                }
            }

            // Recent Posts
            $recentPosts = Cache::get('recentPosts');
            if (is_null($recentPosts)) {
                $recentPosts = Post::query()->recent()->get();
                if ($recentPosts->isNotEmpty()) {
                    Cache::put('recentPosts', $recentPosts, $ttl);
                }
            }

            // Popular Posts
            $popularPosts = Cache::get('popularPosts');
            if (is_null($popularPosts)) {
                $popularPosts = Post::query()->popular()->get();
                if ($popularPosts->isNotEmpty()) {
                    Cache::put('popularPosts', $popularPosts, $ttl);
                }
            }

            $view->with(compact('categories', 'tags', 'recentPosts', 'popularPosts'));
        });

        Carbon::setLocale(config('app.locale'));
    }
}
