<div id="comments-list">
    @forelse($thread->comments as $comment)
        @include('frontpages.threads.partials._comment', ['comment' => $comment])
    @empty
        <p class="text-muted">Belum ada komentar. Jadilah yang pertama.</p>
    @endforelse
</div>
