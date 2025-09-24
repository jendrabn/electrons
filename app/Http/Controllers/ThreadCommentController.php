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

            return redirect()->route('comunity.show', $thread->slug)->with('error', $message);
        }

        // record the hit with 30s decay
        RateLimiter::hit($key, 30);

        // trim surrounding whitespace/newlines/non-breaking spaces from HTML body
        $body = $this->trimHtmlEdges($data['body']);

        // if body becomes empty after trimming, return error
        if (trim(strip_tags(str_replace('&nbsp;', '', $body))) === '') {
            $message = 'Komentar tidak boleh kosong.';
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => $message], 422);
            }
            return redirect()->route('comunity.show', $thread->slug)->with('error', $message);
        }

        $comment = $thread->comments()->create([
            'user_id' => Auth::id(),
            'body' => $body,
            'parent_id' => $data['parent_id'] ?? null,
            'is_hidden' => false,
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Komentar ditambahkan.', 'id' => $comment->id]);
        }

        return redirect()->route('comunity.show', $thread->slug)->with('status', 'Komentar ditambahkan.');
    }

    public function update(ThreadCommentRequest $request, Thread $thread, ThreadComment $comment)
    {
        $this->authorize('update', $comment);

        $data = $request->validated();

        // trim surrounding whitespace/newlines/non-breaking spaces from HTML body
        $body = $this->trimHtmlEdges($data['body']);

        if (trim(strip_tags(str_replace('&nbsp;', '', $body))) === '') {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Komentar tidak boleh kosong.'], 422);
            }
            return redirect()->route('comunity.show', $thread->slug)->with('error', 'Komentar tidak boleh kosong.');
        }

        $comment->update(['body' => $body]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'body' => $comment->body,
                'id' => $comment->id,
            ]);
        }

        return redirect()->route('comunity.show', $thread->slug)->with('status', 'Komentar diperbarui.');
    }

    public function edit(Request $request, Thread $thread, ThreadComment $comment)
    {
        $this->authorize('update', $comment);

        // return only the HTML form for modal
        if ($request->wantsJson() || $request->ajax()) {
            $html = view('threads.partials._comment_edit_form', compact('comment'))->render();
            return response()->json(['html' => $html]);
        }

        return view('threads.edit', compact('thread', 'comment'));
    }

    public function destroy(Request $request, Thread $thread, ThreadComment $comment)
    {
        $this->authorize('delete', $comment);

        $comment->delete();

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'id' => $comment->id]);
        }

        return redirect()->route('comunity.show', $thread->slug)->with('status', 'Komentar dihapus.');
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
            $liked = false;
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

        return redirect()->route('comunity.show', $thread->slug)->with('status', 'Komentar ditandai sebagai jawaban terbaik.');
    }

    /**
     * Trim whitespace/newlines and non-breaking spaces from the edges of an HTML string.
     */
    private function trimHtmlEdges(string $html): string
    {
        // Normalize non-breaking spaces to regular spaces for trimming
        $s = str_replace('&nbsp;', ' ', $html);

        // Remove leading/trailing whitespace including newlines
        $s = preg_replace('/^[\s\x{00A0}\x{FEFF}]+|[\s\x{00A0}\x{FEFF}]+$/u', '', $s);

        // Re-normalize multiple spaces and keep HTML tags intact; we return trimmed string
        return $s;
    }
}
