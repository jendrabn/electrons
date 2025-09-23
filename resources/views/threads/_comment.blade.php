{{-- filepath: e:\Code\PHP\electrons\resources\views\threads\_comment.blade.php --}}
<div class="comment mb-4"
     data-id="{{ $comment->id }}">
    <div class="d-flex align-items-start">
        <img alt="{{ $comment->user->name }}"
             class="rounded-circle me-3"
             src="{{ $comment->user->avatar_url }}"
             style="width:40px;height:40px;object-fit:cover;">
        <div class="flex-grow-1">
            <div class="d-flex align-items-center mb-1">
                <span class="fw-semibold me-2">{{ $comment->user->name }}</span>
                <span class="text-muted small">@{{ $comment - > user - > username }}</span>
                <span class="text-muted small ms-2">{{ $comment->created_at->diffForHumans() }}</span>
                <div class="ms-auto">
                    <button class="btn btn-link btn-sm p-0 text-decoration-none comment-like-btn"
                            data-id="{{ $comment->id }}">
                        <i
                           class="bi {{ $comment->likes->where('user_id', auth()->id())->count() ? 'bi-heart-fill text-danger' : 'bi-heart' }}"></i>
                        <span class="comment-like-count">{{ $comment->likes->count() }}</span>
                    </button>
                </div>
            </div>
            <div class="mb-2">{!! $comment->body !!}</div>
            <div class="d-flex align-items-center gap-3">
                @auth
                    <button class="btn btn-link btn-sm p-0 text-decoration-none reply-btn"
                            data-id="{{ $comment->id }}"
                            data-username="{{ $comment->user->username }}"
                            type="button">
                        <i class="bi bi-reply"></i> Balas
                    </button>
                @endauth
            </div>
            {{-- Form Reply (hidden by default) --}}
            <form action="{{ route('thread-comments.reply', [$thread->id, $comment->id]) }}"
                  class="reply-form mt-3 d-none"
                  data-id="{{ $comment->id }}"
                  method="POST">
                @csrf
                <input name="parent_id"
                       type="hidden"
                       value="{{ $comment->id }}">
                <textarea class="form-control mb-2"
                          name="body"
                          placeholder="Tulis balasan..."
                          required
                          rows="2"></textarea>
                <button class="btn btn-secondary btn-sm"
                        type="submit">Kirim Balasan</button>
            </form>
            {{-- List Reply --}}
            @if ($comment->replies()->count() > 0)
                <button class="btn btn-link btn-sm p-0 text-decoration-none"
                        data-bs-target="#repliesCollapse{{ $comment->id }}"
                        data-bs-toggle="collapse"
                        type="button">
                    <i class="bi bi-chat-left-text"></i> Lihat {{ $comment->replies()->count() }} Balasan
                </button>
                <div class="collapse mt-2"
                     id="repliesCollapse{{ $comment->id }}">
                    @foreach ($comment->replies()->latest()->get() as $reply)
                        @include('threads._reply', ['reply' => $reply])
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
