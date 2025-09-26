<div id="comments-list">
    @php
        $topLevel = $post
            ->comments()
            ->whereNull('parent_id')
            ->with(['user', 'likes', 'replies.user', 'replies.likes'])
            ->get();
    @endphp

    @forelse($topLevel as $comment)
        @include('posts.partials._comment', ['comment' => $comment, 'post' => $post])
    @empty
        <div class="text-muted">Belum ada komentar. Jadilah yang pertama menulis komentar!</div>
    @endforelse
</div>
