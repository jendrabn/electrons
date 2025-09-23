{{-- filepath: e:\Code\PHP\electrons\resources\views\threads\_reply.blade.php --}}
<div class="comment reply mt-3 ms-4"
     data-id="{{ $reply->id }}">
    <div class="d-flex align-items-start">
        <img alt="{{ $reply->user->name }}"
             class="rounded-circle me-2"
             src="{{ $reply->user->avatar_url }}"
             style="width:32px;height:32px;object-fit:cover;">
        <div class="flex-grow-1">
            <div class="d-flex align-items-center mb-1">
                <span class="fw-semibold me-2">{{ $reply->user->name }}</span>
                <span class="text-muted small">@{{ $reply - > user - > username }}</span>
                <span class="text-muted small ms-2">{{ $reply->created_at->diffForHumans() }}</span>
                <div class="ms-auto">
                    <button class="btn btn-link btn-sm p-0 text-decoration-none reply-like-btn"
                            data-id="{{ $reply->id }}">
                        <i
                           class="bi {{ $reply->likes->where('user_id', auth()->id())->count() ? 'bi-heart-fill text-danger' : 'bi-heart' }}"></i>
                        <span class="reply-like-count">{{ $reply->likes->count() }}</span>
                    </button>
                </div>
            </div>
            <div class="mb-2">
                @if ($reply->parent)
                    <span class="text-muted small">@{{ $reply - > parent - > user - > username }}</span>
                @endif
                {!! $reply->body !!}
            </div>
            <div class="d-flex align-items-center gap-3">
                @auth
                    <button class="btn btn-link btn-sm p-0 text-decoration-none reply-btn"
                            data-id="{{ $reply->id }}"
                            data-username="{{ $reply->user->username }}"
                            type="button">
                        <i class="bi bi-reply"></i> Balas
                    </button>
                @endauth
            </div>
            {{-- Form Reply (hidden by default) --}}
            <form action="{{ route('thread-comments.reply', [$thread->id, $reply->id]) }}"
                  class="reply-form mt-3 d-none"
                  data-id="{{ $reply->id }}"
                  method="POST">
                @csrf
                <input name="parent_id"
                       type="hidden"
                       value="{{ $reply->id }}">
                <textarea class="form-control mb-2"
                          name="body"
                          placeholder="Tulis balasan..."
                          required
                          rows="2"></textarea>
                <button class="btn btn-secondary btn-sm"
                        type="submit">Kirim Balasan</button>
            </form>
            {{-- Nested reply (optional, bisa recursive jika ingin reply bertingkat lebih dari 2) --}}
        </div>
    </div>
</div>
