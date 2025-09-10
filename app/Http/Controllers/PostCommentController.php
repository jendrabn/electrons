<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

class PostCommentController extends Controller
{

    /**
     * List comments of a post.
     *
     * @param Post $post
     * @return \Illuminate\Http\JsonResponse
     */
    public function list(Post $post): JsonResponse
    {
        $comments = $post->comments()
            ->with(['user', 'likes', 'replies' => function ($query) {
                $query->with(['user'])->withCount(['likes', 'replies']);
            }])
            ->withCount(['likes', 'replies'])
            ->where('post_id', $post->id)
            ->whereNull('parent_id')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($comments);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    /**
     * Validation rules:
     * post_id: required, exists in posts table
     * body: required, string, min 3, max 1000
     *
     * Rate limiting:
     * 1 request per 30 seconds
     *
     * @throws \Illuminate\Validation\ValidationException
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'post_id' => ['required', 'exists:posts,id'],
            'body' => ['required', 'string', 'min:3', 'max:1000'],
        ]);

        $key = auth()->id() . '|' . $request->ip() . '|' . $request->post_id;

        if (RateLimiter::tooManyAttempts($key, 1)) {
            $seconds = RateLimiter::availableIn($key);

            return response()->json([
                'success' => false,
                'message' => 'Anda dapat mengirim komentar lagi dalam ' . $seconds . ' detik.',
            ], 429);
        }

        RateLimiter::hit($key, 30);

        Comment::create(array_merge($request->all(), ['user_id' => auth()->id()]));

        return response()->json([
            'success' => true,
            'message' => 'Komentar berhasil dikirim.',
        ]);
    }

    /**
     * Update the specified comment in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Comment  $comment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Comment $comment): JsonResponse
    {
        $request->validate([
            'body' => ['required', 'string', 'min:3', 'max:1000'],
        ]);

        $comment->update(['body' => $request->body]);

        return response()->json([
            'success' => true,
            'message' => 'Komentar berhasil diperbarui.',
        ]);
    }

    /**
     * Delete the specified comment in storage.
     *
     * @param  \App\Models\Comment  $comment
     * @return \Illuminate\Http\Response
     */
    public function destroy(Comment $comment): JsonResponse
    {
        $comment->delete();

        return response()->json([
            'success' => true,
            'message' => 'Komentar berhasil dihapus.',
        ]);
    }

    /**
     * Reply to the specified comment.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Comment  $comment
     * @return \Illuminate\Http\Response
     */
    public function reply(Request $request, Comment $comment): JsonResponse
    {
        $request->validate([
            'body' => ['required', 'string', 'min:3', 'max:1000'],
        ]);

        $comment->replies()->create([
            'user_id' => auth()->id(),
            'post_id' => $comment->post_id,
            'body' => $request->body,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Balasan berhasil dikirim.',
        ]);
    }

    /**
     * Like or unlike a comment.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Comment  $comment
     * @return \Illuminate\Http\Response
     *
     * @throws \Illuminate\Validation\ValidationException
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     */
    public function like(Request $request, Comment $comment): JsonResponse
    {
        if ($comment->likes()->where('user_id', auth()->id())->exists()) {
            $comment->likes()->where('user_id', auth()->id())->delete();

            return response()->json([
                'success' => true,
                'message' => 'Like berhasil dihapus.',
            ]);
        }

        $comment->likes()->create([
            'user_id' => auth()->id(),
        ]);
        return response()->json([
            'success' => true,
            'message' => 'Like berhasil dikirim.',
        ]);
    }
}
