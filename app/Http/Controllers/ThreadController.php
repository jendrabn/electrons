<?php

namespace App\Http\Controllers;

use App\Http\Requests\ThreadRequest;
use App\Models\Thread;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\ThreadBookmark;
use Illuminate\Support\Facades\Auth;
use Artesaos\SEOTools\Facades\SEOTools;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class ThreadController extends Controller
{
    public function index(Request $request)
    {
        $topContributors = User::withCount('threadComments')->orderBy('thread_comments_count', 'desc')->take(5)->get();

        // include counts for comments and likes so the view can show totals
        $threadsQuery = Thread::with(['user', 'tags'])
            ->withCount(['comments', 'likes']);

        $filter = $request->query('filter');
        $tagFilter = $request->query('tag');
        $currentTag = null;

        // apply filters
        // category filter (accept slug or id)
        if (!empty($tagFilter)) {
            $threadsQuery->whereHas('tags', function ($q) use ($tagFilter) {
                if (is_numeric($tagFilter)) {
                    $q->where('tags.id', $tagFilter);
                } else {
                    $q->where('tags.slug', $tagFilter);
                }
            });
            // resolve current tag for UI title
            $currentTag = Tag::where('slug', $tagFilter)
                ->orWhere('id', $tagFilter)
                ->first();
        }
        if ($filter === 'mine' && Auth::check()) {
            $threadsQuery->where('user_id', Auth::id());
        }

        if ($filter === 'bookmarks' && Auth::check()) {
            $threadsQuery->whereHas('bookmarks', function ($q) {
                $q->where('user_id', Auth::id());
            });
        }

        if ($filter === 'answered') {
            $threadsQuery->where('is_done', true);
        }

        // Apply search after other filters: prefer fulltext if available but
        // additionally prioritize title prefix/contains matches so partial
        // tokens like "liver" can match "Liverpool". Use the existing
        // $threadsQuery (preserves filters) and add ordering.
        $search = trim((string) $request->query('search', ''));

        if (strlen($search) > 0) {
            // Use LIKE-based search on title and categories only (no body, no FULLTEXT).
            // Prioritize:
            // 1) title starts with query
            // 2) title contains query
            // 3) category name or slug contains query
            $likePrefix = $search . '%';
            $likeContains = '%' . $search . '%';

            $threadsQuery->where(function ($q) use ($likePrefix, $likeContains) {
                $q->where('title', 'like', $likePrefix)
                    ->orWhere('title', 'like', $likeContains)
                    ->orWhereHas('tags', function ($c) use ($likeContains) {
                        $c->where('name', 'like', $likeContains)
                            ->orWhere('slug', 'like', $likeContains);
                    });
            })->orderByRaw("CASE WHEN title LIKE ? THEN 0 WHEN title LIKE ? THEN 1 ELSE 2 END", [$likePrefix, $likeContains])
                ->orderByDesc('updated_at');
        } else {
            // no search: order by latest
            $threadsQuery->latest();
        }

        $threads = $threadsQuery->with(['user', 'tags'])
            ->withCount(['comments', 'likes'])
            ->paginate(10)
            ->withQueryString();

        $tags = Tag::all();

        // SEO
        SEOTools::setTitle('Komunitas - ' . config('app.name'));
        SEOTools::setDescription('Temukan diskusi, tanya jawab, dan solusi teknis di komunitas kami. Jelajahi thread terbaru dan top contributor.');
        SEOTools::setCanonical(route('comunity.index'));
        SEOTools::opengraph()->setUrl(route('comunity.index'));
        SEOTools::opengraph()->addProperty('type', 'website');

        return view('threads.index', compact('threads', 'tags', 'topContributors', 'filter', 'currentTag'));
    }

    public function create()
    {
        $tags = Tag::all()->pluck('name', 'id');

        // SEO for create thread page
        SEOTools::setTitle('Buat Thread - ' . config('app.name'));
        SEOTools::setDescription('Buat thread baru untuk berdiskusi atau meminta bantuan. Berikan judul yang jelas dan jelaskan masalah Anda.');
        SEOTools::setCanonical(route('comunity.create'));
        SEOTools::opengraph()->setUrl(route('comunity.create'));

        return view('threads.create', compact('tags'));
    }

    public function store(\App\Http\Requests\ThreadRequest $request)
    {
        $data = $request->validated();


        $thread = Thread::create($data);

        $thread->tags()->attach($data['tag_ids'] ?? []);

        return redirect()->route('comunity.show', $thread->id)->with('status', 'Thread berhasil dibuat.');
    }

    public function show(Thread $thread)
    {
        $thread->load(['user', 'tags', 'bookmarks', 'comments' => function ($q) {
            // only load top-level comments (parent_id = NULL), exclude hidden ones
            $q->where('is_hidden', false)
                ->whereNull('parent_id')
                ->with(['user', 'replies' => function ($r) {
                    $r->where('is_hidden', false)->with(['user', 'likes'])->orderBy('created_at', 'asc');
                }, 'likes'])
                // show any comment marked as best answer first, then newest comments
                ->orderByDesc('is_best_answer')
                ->orderByDesc('created_at');
        }]);

        // SEO for thread show
        $title = $thread->title . ' - ' . config('app.name');
        $description = Str::limit(strip_tags($thread->body), 160);
        SEOTools::setTitle($title);
        SEOTools::setDescription($description ?: config('app.name'));
        SEOTools::setCanonical(route('comunity.show', $thread->id));
        SEOTools::opengraph()->setUrl(route('comunity.show', $thread->id));
        SEOTools::opengraph()->addProperty('type', 'article');
        SEOTools::opengraph()->setTitle($title);
        SEOTools::opengraph()->setDescription($description);
        // add image if user avatar exists
        if (!empty($thread->user->avatar_url)) {
            SEOTools::opengraph()->addImage($thread->user->avatar_url);
        }

        return view('threads.show', compact('thread'));
    }

    public function edit(Thread $thread)
    {
        // only thread owner may edit
        $this->authorize('update', $thread);

        $tags = Tag::all()->pluck('name', 'id');

        // SEO for edit page
        $title = 'Edit: ' . $thread->title . ' - ' . config('app.name');
        SEOTools::setTitle($title);
        SEOTools::setDescription('Edit thread Anda — pastikan judul dan konten jelas agar komunitas dapat membantu.');
        SEOTools::setCanonical(route('comunity.edit', $thread->id));
        SEOTools::opengraph()->setUrl(route('comunity.edit', $thread->id));

        return view('threads.edit', compact('thread', 'tags'));
    }

    public function update(\App\Http\Requests\ThreadRequest $request, Thread $thread)
    {
        $this->authorize('update', $thread);

        $data = $request->validated();


        $thread->update($data);

        $thread->tags()->sync($data['tag_ids'] ?? []);

        return redirect()->route('comunity.show', $thread->id)->with('status', 'Thread berhasil diperbarui.');
    }

    public function destroy(Thread $thread)
    {
        $this->authorize('delete', $thread);

        $thread->delete();

        return redirect()->route('comunity.index')->with('status', 'Thread dihapus.');
    }

    public function uploadImage(Request $request, Thread $thread)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $file = $request->file('image');

        $path = $file->store('uploads/threads', 'public');

        return response()->json([
            'success' => true,
            'url' => asset('storage/' . $path),
        ]);
    }

    /**
     * Toggle the thread 'is_done' state. Only the thread owner (or authorized user)
     * may toggle. Returns JSON for AJAX requests, otherwise redirects back.
     */
    public function toggleDone(Request $request, Thread $thread)
    {
        // authorize via policy (owner-only)
        $this->authorize('toggleDone', $thread);

        $thread->is_done = ! (bool) $thread->is_done;
        $thread->save();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'is_done' => (bool) $thread->is_done]);
        }

        return redirect()->back()->with('status', $thread->is_done ? 'Thread ditandai sudah terjawab.' : 'Thread dibuka kembali.');
    }

    /**
     * Toggle a bookmark for the authenticated user on this thread.
     * Returns JSON for AJAX requests, otherwise redirects back.
     */
    public function toggleBookmark(Request $request, Thread $thread)
    {
        if (!Auth::check()) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
            }
            return redirect()->route('auth.show.login');
        }

        $userId = Auth::id();

        $existing = ThreadBookmark::where('user_id', $userId)
            ->where('thread_id', $thread->id)
            ->first();

        $bookmarked = false;
        if ($existing) {
            $existing->delete();
            $bookmarked = false;
        } else {
            ThreadBookmark::create([
                'user_id' => $userId,
                'thread_id' => $thread->id,
            ]);
            $bookmarked = true;
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'bookmarked' => $bookmarked,
                'id' => $thread->id,
            ]);
        }

        return redirect()->back()->with('status', $bookmarked ? 'Thread ditandai sebagai favorit.' : 'Thread dihapus dari favorit.');
    }

    /**
     * Toggle like for a thread. Moved from ThreadLikeController.
     */
    public function like(Request $request, Thread $thread)
    {
        $userId = Auth::id();

        $existing = \App\Models\Like::where('likeable_type', Thread::class)
            ->where('likeable_id', $thread->id)
            ->where('user_id', $userId)
            ->first();

        $liked = false;
        if ($existing) {
            $existing->delete();
            $liked = false;
        } else {
            \App\Models\Like::create([
                'user_id' => $userId,
                'likeable_type' => Thread::class,
                'likeable_id' => $thread->id,
            ]);
            $liked = true;
        }

        $count = \App\Models\Like::where('likeable_type', Thread::class)
            ->where('likeable_id', $thread->id)
            ->count();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'liked' => $liked,
                'count' => $count,
                'id' => $thread->id,
            ]);
        }

        return back()->with('status', $liked ? 'Thread disukai.' : 'Thread tidak disukai.');
    }

    /**
     * Suggest thread titles for autocomplete while typing.
     */
    public function suggest(Request $request)
    {
        $q = trim($request->query('q', ''));

        if (strlen($q) < 3) {
            return response()->json(['suggestions' => []]);
        }

        // For suggestions prefer title and category matches only (no body).
        // Priority:
        // 1) title starts with query
        // 2) title contains query
        // 3) category name or slug contains query
        // This ensures prefix matches like 'liver' -> 'Liverpool' are returned first.

        $likePrefix = $q . '%';
        $likeContains = '%' . $q . '%';

        $results = Thread::select('threads.id', 'threads.title', 'threads.slug')
            ->where(function ($wb) use ($likePrefix, $likeContains) {
                $wb->where('title', 'like', $likePrefix)
                    ->orWhere('title', 'like', $likeContains)
                    ->orWhereHas('tags', function ($c) use ($likeContains) {
                        $c->where('name', 'like', $likeContains)
                            ->orWhere('slug', 'like', $likeContains);
                    });
            })
            ->orderByRaw("CASE WHEN title LIKE ? THEN 0 WHEN title LIKE ? THEN 1 ELSE 2 END", [$likePrefix, $likeContains])
            ->orderByDesc('threads.updated_at')
            ->limit(10)
            ->get();

        $suggestions = $results->map(function ($t) {
            return ['id' => $t->id, 'title' => $t->title, 'url' => route('comunity.show', $t->id)];
        })->values();

        return response()->json(['suggestions' => $suggestions]);
    }
}
