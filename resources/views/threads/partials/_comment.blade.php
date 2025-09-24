<div class="comment mb-4"
     id="comment-{{ $comment->id }}">
    <div class="d-flex align-items-start">
        <img alt="{{ $comment->user->name }}"
             class="rounded-circle me-3"
             src="{{ $comment->user->avatar_url }}"
             style="width:40px;height:40px;object-fit:cover;">

        <div class="flex-grow-1">
            <div class="d-flex align-items-center mb-1">
                <div class="flex-grow-1">
                    <div class="d-flex align-items-center gap-2">
                        <a class="fw-semibold text-decoration-none"
                           href="{{ route('users.show', $comment->user->id) }}">{{ $comment->user->name }}</a>
                        @if ($comment->is_best_answer)
                            <span class="badge bg-gradient-green text-white">Best Answer</span>
                        @endif
                    </div>

                    <div class="d-flex align-items-center gap-2">
                        <a class="text-info small text-decoration-none"
                           href="{{ route('users.show', $comment->user->id) }}">{{ '@' . $comment->user->username }}</a>
                        <div class="text-muted small ms-1">{{ $comment->updated_at->diffForHumans() }}</div>
                    </div>
                </div>

                @php $threadDone = isset($thread) ? (bool) $thread->is_done : false; @endphp
                @if (auth()->check() && auth()->id() === $comment->user_id && !$threadDone)
                    <div class="ms-3 d-flex gap-2">
                        <button aria-label="Edit komentar"
                                class="btn btn-link btn-sm p-0 text-decoration-none edit-btn comment-edit"
                                data-id="{{ $comment->id }}"
                                title="Edit komentar"
                                type="button">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <button aria-label="Hapus komentar"
                                class="btn btn-link btn-sm p-0 text-decoration-none text-danger delete-btn comment-delete"
                                data-id="{{ $comment->id }}"
                                data-url="{{ route('comunity.comments.destroy', [$comment->thread->slug, $comment->id]) }}"
                                title="Hapus komentar"
                                type="button">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                @endif
            </div>

            <div
                 class="mb-2 comment-body {{ $comment->is_best_answer ? 'bg-warning bg-opacity-10 p-2 rounded' : '' }}">
                {!! $comment->body !!}</div>

            <div class="d-flex align-items-center gap-3">
                @if (auth()->check())
                    <button aria-label="Balas komentar"
                            class="btn btn-link btn-sm p-0 text-decoration-none reply-btn"
                            data-id="{{ $comment->id }}"
                            title="Balas komentar"
                            type="button">
                        <i class="bi bi-reply"></i> Balas
                    </button>
                @endif

                <button aria-label="Suka komentar"
                        class="btn btn-link btn-sm p-0 text-decoration-none like-btn comment-like-btn"
                        data-id="{{ $comment->id }}"
                        data-url="{{ route('comunity.comments.like', [$comment->thread->slug, $comment->id]) }}"
                        title="Suka"
                        type="button">
                    <i
                       class="bi {{ $comment->likes->where('user_id', auth()->id())->count() ? 'bi-hand-thumbs-up-fill text-primary' : 'bi-hand-thumbs-up' }}"></i>
                    <span class="like-count">{{ $comment->likes->count() }}</span>
                </button>

                @if ($comment->replies->count())
                    <button class="btn btn-link btn-sm p-0 text-decoration-none"
                            data-bs-target="#repliesCollapse{{ $comment->id }}"
                            data-bs-toggle="collapse"
                            type="button">
                        <i class="bi bi-chat-left-text"></i> Lihat {{ $comment->replies->count() }} Balasan
                    </button>
                @endif
            </div>

            {{-- optional inline reply form (hidden by default) --}}
            <form class="reply-form mt-3 d-none"
                  data-id="{{ $comment->id }}">
                <textarea class="form-control mb-2"
                          name="body"
                          placeholder="Tulis balasan..."
                          required
                          rows="2"></textarea>
                <button class="btn btn-primary btn-sm"
                        type="submit"><i class="bi bi-send"></i> Kirim Balasan</button>
            </form>

            @if ($comment->replies->count())
                <div class="collapse mt-2"
                     id="repliesCollapse{{ $comment->id }}">
                    @foreach ($comment->replies as $reply)
                        <div class="comment reply mt-3 ms-4"
                             data-id="{{ $reply->id }}">
                            <div class="d-flex align-items-start">
                                <img alt="{{ $reply->user->name }}"
                                     class="rounded-circle me-2"
                                     src="{{ $reply->user->avatar_url }}"
                                     style="width:32px;height:32px;object-fit:cover;">
                                <div class="flex-grow-1">
                                    <div class="d-flex align-items-center mb-1">
                                        <div class="flex-grow-1">
                                            <div class="d-flex align-items-center gap-2">
                                                <a class="fw-semibold text-decoration-none"
                                                   href="{{ route('users.show', $reply->user->id) }}">{{ $reply->user->name }}</a>
                                            </div>

                                            <div class="d-flex align-items-center gap-2">
                                                <a class="text-primary small text-decoration-none"
                                                   href="{{ route('users.show', $reply->user->id) }}">{{ '@' . $reply->user->username }}</a>
                                                <div class="text-muted small ms-1">
                                                    {{ $reply->updated_at->diffForHumans() }}</div>
                                            </div>
                                        </div>

                                        @if (auth()->check() && auth()->id() === $reply->user_id)
                                            <div class="ms-3 d-flex gap-2">
                                                <button aria-label="Edit komentar"
                                                        class="btn btn-link btn-sm p-0 text-decoration-none edit-btn comment-edit"
                                                        data-id="{{ $reply->id }}"
                                                        title="Edit komentar"
                                                        type="button">
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                                <button aria-label="Hapus komentar"
                                                        class="btn btn-link btn-sm p-0 text-decoration-none text-danger delete-btn comment-delete"
                                                        data-id="{{ $reply->id }}"
                                                        data-url="{{ route('comunity.comments.destroy', [$reply->thread->slug, $reply->id]) }}"
                                                        title="Hapus komentar"
                                                        type="button">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                        @else
                                            <div class="ms-auto">
                                                <button aria-label="Suka komentar"
                                                        class="btn btn-link btn-sm p-0 text-decoration-none like-btn comment-like-btn"
                                                        data-id="{{ $reply->id }}"
                                                        data-url="{{ route('comunity.comments.like', [$reply->thread->slug, $reply->id]) }}"
                                                        title="Suka"
                                                        type="button">
                                                    <i
                                                       class="bi {{ $reply->likes->where('user_id', auth()->id())->count() ? 'bi-hand-thumbs-up-fill text-primary' : 'bi-hand-thumbs-up' }}"></i>
                                                    <span class="like-count">{{ $reply->likes->count() }}</span>
                                                </button>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="mb-2">{!! $reply->body !!}</div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
