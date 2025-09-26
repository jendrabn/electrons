<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostCommentRequest;
use App\Models\PostComment;
use App\Models\Post;
use App\Models\Like;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Auth;

class PostCommentController extends Controller
{
    public function store(PostCommentRequest $request, Post $post)
    {
        $this->authorize('create', PostComment::class);

        $data = $request->validated();

        // Rate limiter: allow 1 comment per 30 seconds per user+ip+post
        $key = auth()->id() . '|' . $request->ip() . '|' . $post->id;
        if (RateLimiter::tooManyAttempts($key, 1)) {
            $seconds = RateLimiter::availableIn($key);

            $message = 'Tunggu ' . $seconds . ' detik sebelum mengirim komentar lagi.';

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => $message], 429);
            }

            return redirect()->route('posts.show', $post)->with('error', $message);
        }

        // record the hit with 30s decay
        RateLimiter::hit($key, 30);

        $body = $data['body'];

        // Ensure parent_id always points to the top-level comment for this post.
        $parentId = null;
        if (!empty($data['parent_id'])) {
            $target = PostComment::where('id', $data['parent_id'])->where('post_id', $post->id)->first();
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

        $comment = $post->comments()->create([
            'user_id' => Auth::id(),
            'body' => $body,
            'parent_id' => $parentId,
            // 'is_hidden' => false,
        ]);


        // If AJAX request, render the reply partial HTML and return it with updated reply count
        if ($request->wantsJson() || $request->ajax()) {
            $html = view('posts.partials._reply', ['reply' => $comment, 'post' => $post])->render();
            $count = PostComment::where('post_id', $post->id)->where('parent_id', $parentId)->count();
            return response()->json(['success' => true, 'message' => 'Komentar ditambahkan.', 'id' => $comment->id, 'parent_id' => $parentId, 'html' => $html, 'count' => $count]);
        }

        return redirect()->route('posts.show', $post)->with('status', 'Komentar ditambahkan.');
    }

    public function update(PostCommentRequest $request, Post $post, PostComment $comment)
    {
        $this->authorize('update', $comment);

        $data = $request->validated();

        $body = $data['body'];

        if (trim(strip_tags(str_replace('&nbsp;', '', $body))) === '') {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Komentar tidak boleh kosong.'], 422);
            }
            return redirect()->route('posts.show', $post)->with('error', 'Komentar tidak boleh kosong.');
        }

        $comment->update(['body' => $body]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'body' => $comment->body,
                'id' => $comment->id,
            ]);
        }

        return redirect()->route('posts.show', $post)->with('status', 'Komentar diperbarui.');
    }

    public function edit(Request $request, Post $post, PostComment $comment)
    {
        $this->authorize('update', $comment);

        // return only the HTML form for modal
        if ($request->wantsJson() || $request->ajax()) {

            if ($comment->parent_id) {
                // it's a reply, load the reply edit form
                $html = view('posts.partials._reply_edit_form', compact('comment', 'post'))->render();
                return response()->json(['html' => $html]);
            } else {
                // it's a top-level comment, load the comment edit form
                $html = view('posts.partials._comment_edit_form', compact('comment', 'post'))->render();
                return response()->json(['html' => $html]);
            }
        }

        return view('posts.edit', compact('post', 'comment'));
    }

    public function destroy(Request $request, Post $post, PostComment $comment)
    {
        $this->authorize('delete', $comment);

        // remember parent before deleting so we can return updated count for replies
        $parentId = $comment->parent_id;
        $commentId = $comment->id;
        $comment->delete();

        if ($request->wantsJson() || $request->ajax()) {
            $count = null;
            if (!empty($parentId)) {
                $count = PostComment::where('post_id', $post->id)->where('parent_id', $parentId)->count();
            }
            return response()->json(['success' => true, 'id' => $commentId, 'parent_id' => $parentId, 'count' => $count]);
        }

        return redirect()->route('posts.show', $post)->with('status', 'Komentar dihapus.');
    }

    public function like(Request $request, Post $post, PostComment $comment)
    {
        $userId = Auth::id();

        $existing = Like::where('likeable_type', PostComment::class)
            ->where('likeable_id', $comment->id)
            ->where('user_id', $userId)
            ->first();

        $liked = false;
        if ($existing) {
            $existing->delete();
        } else {
            Like::create([
                'user_id' => $userId,
                'likeable_type' => PostComment::class,
                'likeable_id' => $comment->id,
            ]);
            $liked = true;
        }

        $count = Like::where('likeable_type', PostComment::class)
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
}
