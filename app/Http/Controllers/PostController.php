<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use App\Services\SEOService;
use Artesaos\SEOTools\Facades\JsonLd;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function __construct(private SEOService $seoService) {}

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return View
     */
    public function index(Request $request): View
    {
        $posts = Post::query()->published()->with(['category', 'user', 'tags']);

        $title = 'Blog';
        $searchQuery = $request->input('search');

        $posts->when($request->has('search'), function ($query) use ($request) {
            $keywords = explode(' ', $request->input('search'));
            foreach ($keywords as $keyword) {
                $query->where(function ($q) use ($keyword) {
                    $q->where('title', 'like', '%' . $keyword . '%')
                        ->orWhereHas('tags', function ($q) use ($keyword) {
                            $q->where('name', 'like', '%' . $keyword . '%');
                        })->orWhereHas('category', function ($q) use ($keyword) {
                            $q->where('name', 'like', '%' . $keyword . '%');
                        });
                });
            }
        });

        $posts = $posts->latest()->paginate(10);

        $this->seoService->setPostIndexSEO($searchQuery);

        if ($searchQuery) {
            $title = "Hasil Pencarian: {$searchQuery}";
        }

        return view('posts.index', compact('posts', 'title'));
    }

    /**
     * Display a listing of the resource for a specific category.
     *
     * @param Category $category
     * @return View
     */
    public function category(Category $category): View
    {
        $posts = $category->posts()
            ->published()
            ->with(['category', 'user', 'tags'])
            ->latest()
            ->paginate(10);

        $title = 'Blog ' . $category->name;

        $this->seoService->setCategorySEO($category);

        return view('posts.index', compact('posts', 'title'));
    }

    /**
     * Display a listing of the resource for a specific tag.
     *
     * @param Tag $tag
     * @return View
     */
    public function tag(Tag $tag): View
    {
        $posts = $tag->posts()
            ->published()
            ->with(['category', 'user', 'tags'])
            ->latest()
            ->paginate(10);


        $title = 'Blog ' . $tag->name;

        $this->seoService->setTagSEO($tag);

        return view('posts.index', compact('posts', 'title'));
    }

    /**
     * Display a listing of the resource for a specific author.
     *
     * @param User $user
     * @return View
     */
    public function author(User $user): View
    {
        $posts = $user->posts()
            ->published()
            ->with(['category', 'user', 'tags'])
            ->latest()
            ->paginate(10);


        $title = 'Blog Oleh ' . $user->name;

        $this->seoService->setAuthorSEO($user);

        return view('posts.index', compact('posts', 'title'));
    }

    /**
     * Display the specified resource.
     *
     * @param Post $post
     * @return View
     */
    public function show(Post $post): View
    {
        $post->increment('views_count');
        $post->load(['category', 'user', 'tags']);

        // Related posts by title similarity using full-text search
        $relatedPosts = Post::query()
            ->published()
            ->where('id', '!=', $post->id)
            ->with(['category', 'user', 'tags'])
            ->whereRaw("MATCH(title, slug) AGAINST(? IN NATURAL LANGUAGE MODE)", [$post->title])
            ->orderByRaw("MATCH(title, slug) AGAINST(? IN NATURAL LANGUAGE MODE) DESC", [$post->title])
            ->take(5)
            ->get();

        $this->seoService->setPostSEO($post);

        return view('posts.show', compact(
            'post',
            'relatedPosts',
        ));
    }
}
