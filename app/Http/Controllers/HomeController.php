<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\PostSection;
use App\Services\SEO\HomeSEOService;
use App\Services\SEOService;

class HomeController extends Controller
{
    public function __construct(private SEOService $seoService) {}


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $sections = PostSection::query()
            ->with([
                'posts' => fn($query) => $query->published()->take(3),
                'posts.category',
                'posts.user',
                'posts.tags'
            ])
            ->orderBy('order', 'asc')
            ->get();

        $newPosts = Post::query()
            ->published()
            ->with(['category', 'user', 'tags'])
            ->latest()
            ->take(10)
            ->get();

        $this->seoService->setHomeSEO($newPosts, $sections);

        return view('home', compact(
            'sections',
            'newPosts',
        ));
    }
}
