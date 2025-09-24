<?php

namespace App\Http\Controllers;

use App\Models\Thread;
use App\Models\ThreadComment;
use App\Models\ThreadLike;
use Illuminate\Http\Request;
use App\Http\Requests\ThreadCommentRequest;
use Illuminate\Support\Facades\Auth;

class ThreadReplyController extends Controller
{
    /**
     * Store a reply to a top-level comment. parent comment is provided in route.
     */
    public function store(ThreadCommentRequest $request, Thread $thread, ThreadComment $comment)
    {
        $this->authorize('create', ThreadComment::class);

        $data = $request->validated();

        // ensure parent_id points to the top-level comment
        $parentId = $comment->parent_id ?: $comment->id;

        $body = $data['body'];
        if (trim(strip_tags(str_replace('&nbsp;', '', $body))) === '') {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Balasan tidak boleh kosong.'], 422);
            }
            return redirect()->route('comunity.show', $thread->id)->with('error', 'Balasan tidak boleh kosong.');
        }

        $reply = $thread->comments()->create([
            'user_id' => Auth::id(),
            'body' => $body,
            'parent_id' => $parentId,
            'is_hidden' => false,
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            $html = view('threads.partials._reply_item', ['reply' => $reply, 'thread' => $thread])->render();
            $count = ThreadComment::where('thread_id', $thread->id)->where('parent_id', $parentId)->count();
            return response()->json(['success' => true, 'message' => 'Balasan ditambahkan.', 'id' => $reply->id, 'parent_id' => $parentId, 'html' => $html, 'count' => $count]);
        }

        return redirect()->route('comunity.show', $thread->id)->with('status', 'Balasan ditambahkan.');
    }

    public function edit(Request $request, Thread $thread, ThreadComment $comment, ThreadComment $reply)
    {
        $this->authorize('update', $reply);

        if ($request->wantsJson() || $request->ajax()) {
            $html = view('threads.partials._reply_edit_form', compact('comment', 'reply'))->render();
            return response()->json(['html' => $html]);
        }

        return view('threads.edit', compact('thread', 'comment', 'reply'));
    }

    public function update(ThreadCommentRequest $request, Thread $thread, ThreadComment $comment, ThreadComment $reply)
    {
        $this->authorize('update', $reply);

        $data = $request->validated();
        $body = $data['body'];
        if (trim(strip_tags(str_replace('&nbsp;', '', $body))) === '') {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Balasan tidak boleh kosong.'], 422);
            }
            return redirect()->route('comunity.show', $thread->id)->with('error', 'Balasan tidak boleh kosong.');
        }

        $reply->update(['body' => $body]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'id' => $reply->id, 'body' => $reply->body]);
        }

        return redirect()->route('comunity.show', $thread->id)->with('status', 'Balasan diperbarui.');
    }

    public function destroy(Request $request, Thread $thread, ThreadComment $comment, ThreadComment $reply)
    {
        $this->authorize('delete', $reply);

        $parentId = $reply->parent_id;
        $replyId = $reply->id;
        $reply->delete();

        if ($request->wantsJson() || $request->ajax()) {
            $count = ThreadComment::where('thread_id', $thread->id)->where('parent_id', $parentId)->count();
            return response()->json(['success' => true, 'id' => $replyId, 'parent_id' => $parentId, 'count' => $count]);
        }

        return redirect()->route('comunity.show', $thread->id)->with('status', 'Balasan dihapus.');
    }

    public function like(Request $request, Thread $thread, ThreadComment $comment, ThreadComment $reply)
    {
        $userId = Auth::id();

        $existing = ThreadLike::where('likeable_type', ThreadComment::class)
            ->where('likeable_id', $reply->id)
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
                'likeable_id' => $reply->id,
            ]);
            $liked = true;
        }

        $count = ThreadLike::where('likeable_type', ThreadComment::class)->where('likeable_id', $reply->id)->count();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'liked' => $liked, 'count' => $count, 'id' => $reply->id]);
        }

        return back();
    }
}
