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
                        @if ($comment->is_best_answer)
                            <span class="badge bg-gradient-green text-white">Best Answer</span>
                        @endif
                    </div>

                    <div class="d-flex align-items-center gap-2">
                        <a class="text-primary text-decoration-none"
                           href="{{ route('users.show', $comment->user->id) }}">{{ $comment->user->username }}</a>
                        <small class="text-muted">•</small>
                        <small class="text-muted">{{ $comment->created_at->diffForHumans() }}</small>
                    </div>
                </div>

                @php $threadDone = isset($thread) ? (bool) $thread->is_done : false; @endphp
                <div class="ms-3 d-flex gap-2">

                    {{-- Jawaban Terbaik: only visible to the thread owner --}}
                    @if (auth()->check() && isset($thread) && auth()->id() === $thread->user_id)
                        <form action="{{ route('comunity.comments.markBest', [$comment->thread->id, $comment->id]) }}"
                              class="m-0 p-0"
                              method="POST">
                            @csrf
                            <button aria-label="Jawaban Terbaik"
                                    class="btn btn-link btn-sm p-0 text-decoration-none text-success"
                                    title="Tandai sebagai jawaban terbaik"
                                    type="submit">
                                <i class="bi bi-award"></i>
                            </button>
                        </form>
                    @endif
                    @if (auth()->check() && auth()->id() === $comment->user_id && !$threadDone)
                        <button aria-label="Edit komentar"
                                class="btn btn-link btn-sm p-0 text-decoration-none edit-btn comment-edit"
                                data-id="{{ $comment->id }}"
                                data-url="{{ route('comunity.comments.edit', [$comment->thread->id, $comment->id]) }}"
                                title="Edit komentar"
                                type="button">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <button aria-label="Hapus komentar"
                                class="btn btn-link btn-sm p-0 text-decoration-none text-danger delete-btn comment-delete"
                                data-id="{{ $comment->id }}"
                                data-url="{{ route('comunity.comments.destroy', [$comment->thread->id, $comment->id]) }}"
                                title="Hapus komentar"
                                type="button">
                            <i class="bi bi-trash"></i>
                        </button>
                    @endif
                </div>
            </div>

            {{-- linkify_mentions is provided globally via app/helpers.php --}}

            <div
                 class="mb-2 comment-body {{ $comment->is_best_answer ? 'bg-warning bg-opacity-10 p-2 rounded' : '' }}">
                {!! linkify_mentions($comment->body) !!}</div>

            <div class="d-flex align-items-center gap-3">
                @if (auth()->check() && !$threadDone)
                    <button aria-label="Balas komentar"
                            class="btn btn-link btn-sm p-0 text-decoration-none reply-btn"
                            data-bs-target="#replyCollapse{{ $comment->id }}"
                            data-bs-toggle="collapse"
                            data-id="{{ $comment->id }}"
                            data-username="{{ $comment->user->username }}"
                            title="Balas komentar"
                            type="button">
                        <i class="bi bi-reply"></i> Balas
                    </button>
                @endif

                <button aria-label="Suka komentar"
                        class="btn btn-link btn-sm p-0 text-decoration-none like-btn comment-like-btn"
                        data-id="{{ $comment->id }}"
                        data-url="{{ route('comunity.comments.like', [$comment->thread->id, $comment->id]) }}"
                        title="Suka"
                        type="button">
                    <i
                       class="bi {{ $comment->likes->where('user_id', auth()->id())->count() ? 'bi-hand-thumbs-up-fill text-primary' : 'bi-hand-thumbs-up' }}"></i>
                    <span class="like-count">{{ $comment->likes->count() }}</span>
                </button>

                {{-- @if ($comment->replies->count())
                    <button class="btn btn-link btn-sm p-0 text-decoration-none"
                            data-bs-target="#repliesCollapse{{ $comment->id }}"
                            data-bs-toggle="collapse"
                            type="button">
                        <i class="bi bi-chat-left-text"></i> Lihat {{ $comment->replies->count() }} Balasan
                    </button>
                @endif --}}
                <button class="btn btn-link btn-sm p-0 text-decoration-none"
                        data-bs-target="#repliesCollapse{{ $comment->id }}"
                        data-bs-toggle="collapse"
                        type="button">
                    <i class="bi bi-chat-left-text"></i> Lihat {{ $comment->replies->count() }} Balasan
                </button>
            </div>

            {{-- optional inline reply form wrapped in collapse (hidden by default) --}}
            <div class="collapse mt-3"
                 id="replyCollapse{{ $comment->id }}">
                @auth
                    <form action="{{ route('comunity.comments.store', [$comment->thread->id, $comment->id]) }}"
                          class="reply-form"
                          method="POST">
                        @csrf
                        <input name="parent_id"
                               type="hidden"
                               value="{{ $comment->id }}">
                        <textarea class="form-control mb-2"
                                  name="body"
                                  placeholder="Tulis balasan..."
                                  required
                                  rows="3"></textarea>
                        <div class="text-end">
                            <button class="btn btn-primary btn-sm"
                                    type="submit"><i class="bi bi-send"></i> Kirim Balasan</button>
                        </div>
                    </form>
                @endauth
            </div>

            @if ($comment->replies->count())
                <div class="collapse mt-2"
                     id="repliesCollapse{{ $comment->id }}">
                    @foreach ($comment->replies as $reply)
                        @include('threads.partials._reply', [
                            'reply' => $reply,
                            'comment' => $comment,
                            'thread' => $thread,
                        ])
                    @endforeach
                </div>
            @else
                <div class="collapse mt-2"
                     id="repliesCollapse{{ $comment->id }}">
                </div>
            @endif
        </div>
    </div>
</div>
