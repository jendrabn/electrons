<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\PostComment;
use App\Models\ThreadComment;
use App\Models\User;
use App\Services\SEOService;
use Illuminate\Contracts\View\View;

class AuthorController extends Controller
{
    public function __construct(private SEOService $seoService) {}

    public function show(User $user): View
    {
        $postsQuery = Post::query()
            ->published()
            ->where('user_id', $user->id);

        $articlesCount = (clone $postsQuery)->count();
        $totalViews = (int) (clone $postsQuery)->sum('views_count');

        $contributionsCount = PostComment::query()
            ->where('user_id', $user->id)
            ->whereNull('parent_id')
            ->count()
            + ThreadComment::query()
            ->where('user_id', $user->id)
            ->whereNull('parent_id')
            ->count();

        $posts = (clone $postsQuery)
            ->with(['category', 'user', 'tags'])
            ->withCount(['likes', 'comments'])
            ->latest()
            ->paginate(6);

        $this->seoService->setAuthorSEO($user);

        return view('frontpages.authors.show', compact(
            'user',
            'articlesCount',
            'totalViews',
            'contributionsCount',
            'posts',
        ));
    }

    /**
     * Return rendered posts for an author (used by AJAX "load more").
     * Responds with HTML partial containing <x-post.article> items.
     */
    public function posts(User $user)
    {
        $postsQuery = Post::query()
            ->published()
            ->where('user_id', $user->id)
            ->with(['category', 'user', 'tags'])
            ->withCount(['likes', 'comments'])
            ->latest();

        $posts = $postsQuery->paginate(6);

        // If requested via AJAX, return rendered items only
        if (request()->ajax()) {
            $html = view('frontpages.authors.partials._posts_list', compact('posts'))->render();
            return response()->json([
                'html' => $html,
                'next_page_url' => $posts->nextPageUrl(),
            ]);
        }

        // Fallback: redirect to show page
        return redirect()->route('authors.show', $user->username);
    }
}
