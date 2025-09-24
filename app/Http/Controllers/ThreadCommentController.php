<?php

namespace App\Http\Controllers;

use App\Models\Thread;
use App\Models\ThreadComment;
use App\Models\ThreadLike;
use Illuminate\Http\Request;
use App\Http\Requests\ThreadCommentRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;

class ThreadCommentController extends Controller
{
    public function store(ThreadCommentRequest $request, Thread $thread)
    {
        $this->authorize('create', ThreadComment::class);

        $data = $request->validated();

        // Rate limiter: allow 1 comment per 30 seconds per user+ip+thread
        $key = auth()->id() . '|' . $request->ip() . '|' . $thread->id;
        if (RateLimiter::tooManyAttempts($key, 1)) {
            $seconds = RateLimiter::availableIn($key);

            $message = 'Tunggu ' . $seconds . ' detik sebelum mengirim komentar lagi.';

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => $message], 429);
            }

            return redirect()->route('comunity.show', $thread->id)->with('error', $message);
        }

        // record the hit with 30s decay
        RateLimiter::hit($key, 30);

        $body = $data['body'];

        // if body becomes empty after trimming, return error
        if (trim(strip_tags(str_replace('&nbsp;', '', $body))) === '') {
            $message = 'Komentar tidak boleh kosong.';
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => $message], 422);
            }
            return redirect()->route('comunity.show', $thread->id)->with('error', $message);
        }

        // Ensure parent_id always points to the top-level comment for this thread.
        $parentId = null;
        if (!empty($data['parent_id'])) {
            $target = ThreadComment::where('id', $data['parent_id'])->where('thread_id', $thread->id)->first();
            if ($target) {
                // if the target is itself a reply (has parent_id), use its parent_id (the top-level comment)
                if (!empty($target->parent_id)) {
                    $parentId = $target->parent_id;
                } else {
                    // target is top-level comment
                    $parentId = $target->id;
                }
            }
        }

        $comment = $thread->comments()->create([
            'user_id' => Auth::id(),
            'body' => $body,
            'parent_id' => $parentId,
            'is_hidden' => false,
        ]);

        // If AJAX request, render the reply partial HTML and return it with updated reply count
        if ($request->wantsJson() || $request->ajax()) {
            $html = view('threads.partials._reply_item', ['reply' => $comment, 'thread' => $thread])->render();
            $count = ThreadComment::where('thread_id', $thread->id)->where('parent_id', $parentId)->count();
            return response()->json(['success' => true, 'message' => 'Komentar ditambahkan.', 'id' => $comment->id, 'parent_id' => $parentId, 'html' => $html, 'count' => $count]);
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Komentar ditambahkan.', 'id' => $comment->id]);
        }

        return redirect()->route('comunity.show', $thread->id)->with('status', 'Komentar ditambahkan.');
    }

    public function update(ThreadCommentRequest $request, Thread $thread, ThreadComment $comment)
    {
        $this->authorize('update', $comment);

        $data = $request->validated();

        $body = $data['body'];

        if (trim(strip_tags(str_replace('&nbsp;', '', $body))) === '') {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Komentar tidak boleh kosong.'], 422);
            }
            return redirect()->route('comunity.show', $thread->id)->with('error', 'Komentar tidak boleh kosong.');
        }

        $comment->update(['body' => $body]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'body' => $comment->body,
                'id' => $comment->id,
            ]);
        }

        return redirect()->route('comunity.show', $thread->id)->with('status', 'Komentar diperbarui.');
    }

    public function edit(Request $request, Thread $thread, ThreadComment $comment)
    {
        $this->authorize('update', $comment);

        // return only the HTML form for modal
        if ($request->wantsJson() || $request->ajax()) {
            // Use Quill editor for top-level comments, textarea for replies (defensive)
            if (empty($comment->parent_id)) {
                $html = view('threads.partials._comment_edit_form_quill', compact('comment'))->render();
            } else {
                $html = view('threads.partials._comment_edit_form', compact('comment'))->render();
            }
            return response()->json(['html' => $html]);
        }

        return view('threads.edit', compact('thread', 'comment'));
    }

    public function destroy(Request $request, Thread $thread, ThreadComment $comment)
    {
        $this->authorize('delete', $comment);

        // remember parent before deleting so we can return updated count for replies
        $parentId = $comment->parent_id;
        $commentId = $comment->id;
        $comment->delete();

        if ($request->wantsJson() || $request->ajax()) {
            $count = null;
            if (!empty($parentId)) {
                $count = ThreadComment::where('thread_id', $thread->id)->where('parent_id', $parentId)->count();
            }
            return response()->json(['success' => true, 'id' => $commentId, 'parent_id' => $parentId, 'count' => $count]);
        }

        return redirect()->route('comunity.show', $thread->id)->with('status', 'Komentar dihapus.');
    }

    public function like(Request $request, Thread $thread, ThreadComment $comment)
    {
        $userId = Auth::id();

        $existing = ThreadLike::where('likeable_type', ThreadComment::class)
            ->where('likeable_id', $comment->id)
            ->where('user_id', $userId)
            ->first();

        $liked = false;
        if ($existing) {
            $existing->delete();
            // dd($request->all()); // Commented out for debugging purposes
        } else {
            ThreadLike::create([
                'user_id' => $userId,
                'likeable_type' => ThreadComment::class,
                'likeable_id' => $comment->id,
            ]);
            $liked = true;
        }

        $count = ThreadLike::where('likeable_type', ThreadComment::class)
            ->where('likeable_id', $comment->id)
            ->count();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'liked' => $liked,
                'count' => $count,
                'id' => $comment->id,
            ]);
        }

        return back();
    }

    public function markBest(Thread $thread, ThreadComment $comment): RedirectResponse
    {
        $this->authorize('markBest', $comment);

        // reset any previous best
        $thread->comments()->where('is_best_answer', true)->update(['is_best_answer' => false]);

        $comment->update(['is_best_answer' => true]);

        return redirect()->route('comunity.show', $thread->id)->with('status', 'Komentar ditandai sebagai jawaban terbaik.');
    }
}
