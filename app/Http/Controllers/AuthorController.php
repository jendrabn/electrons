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
            ->paginate(12);

        $this->seoService->setAuthorSEO($user);

        return view('frontpages.authors.show', compact(
            'user',
            'articlesCount',
            'totalViews',
            'contributionsCount',
            'posts',
        ));
    }
}
