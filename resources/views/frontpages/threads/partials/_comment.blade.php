<div class="comment mb-4"
     id="comment-{{ $comment->id }}">
    <div class="d-flex align-items-start gap-3">
        <img alt="{{ $comment->user->name }}"
             class="rounded-circle border flex-shrink-0"
             height="40"
             src="{{ $comment->user->avatar_url }}"
             style="object-fit:cover"
             width="40">

        <div class="flex-grow-1">
            <div class="d-flex align-items-center mb-1">
                <div class="flex-grow-1">
                    <div class="d-flex align-items-center gap-2">
                        @if ($comment->is_best_answer)
                            <span
                                  class="badge d-inline-flex align-items-center gap-1 text-success-emphasis bg-success-subtle border border-success-subtle">
                                <i aria-hidden="true"
                                   class="bi bi-check-circle-fill"></i>
                                Best Answer
                            </span>
                        @endif
                    </div>

                    <div class="d-flex align-items-center gap-2">
                        <a class="fw-semibold text-decoration-none link-body-emphasis"
                           href="{{ route('authors.show', $comment->user->username) }}">
                            {{ $comment->user->username }}
                        </a>
                        <small class="text-body-secondary">•</small>
                        <small class="text-body-secondary">
                            {{ $comment->created_at->diffForHumans() }}
                        </small>
                    </div>
                </div>

                @php $threadDone = isset($thread) ? (bool) $thread->is_done : false; @endphp
                <div class="ms-3 d-flex gap-2">
                    {{-- Jawaban Terbaik: hanya untuk pemilik thread --}}
                    @if (auth()->check() && isset($thread) && auth()->id() === $thread->user_id)
                        <form action="{{ route('community.comments.markBest', [$comment->thread->id, $comment->id]) }}"
                              class="m-0 p-0"
                              method="POST">
                            @csrf
                            <button aria-label="Jawaban Terbaik"
                                    class="btn btn-link btn-sm p-0 text-decoration-none link-success"
                                    title="Tandai sebagai jawaban terbaik"
                                    type="submit">
                                <i aria-hidden="true"
                                   class="bi bi-award"></i>
                            </button>
                        </form>
                    @endif

                    {{-- Edit: only comment owner can edit (admin cannot edit others) --}}
                    @if (auth()->check() && auth()->id() === $comment->user_id && !$threadDone)
                        <button aria-label="Edit komentar"
                                class="btn btn-link btn-sm p-0 text-decoration-none link-body-emphasis edit-btn comment-edit"
                                data-id="{{ $comment->id }}"
                                data-url="{{ route('community.comments.edit', [$comment->thread->id, $comment->id]) }}"
                                title="Edit komentar"
                                type="button">
                            <i aria-hidden="true"
                               class="bi bi-pencil"></i>
                        </button>
                    @endif

                    {{-- Delete: comment owner OR admin can delete --}}
                    @if (auth()->check() && (auth()->id() === $comment->user_id || auth()->user()->isAdmin()) && !$threadDone)
                        <button aria-label="Hapus komentar"
                                class="btn btn-link btn-sm p-0 text-decoration-none link-danger delete-btn comment-delete"
                                data-id="{{ $comment->id }}"
                                data-url="{{ route('community.comments.destroy', [$comment->thread->id, $comment->id]) }}"
                                title="Hapus komentar"
                                type="button">
                            <i aria-hidden="true"
                               class="bi bi-trash"></i>
                        </button>
                    @endif
                </div>
            </div>

            {{-- body komentar (highlight jika Best Answer) --}}
            <div @class([
                'mb-2 text-body text-break text-wrap comment-body',
                'bg-warning-subtle border border-warning-subtle rounded p-2' =>
                    $comment->is_best_answer,
            ])>
                {!! linkify_mentions($comment->body) !!}
            </div>

            <div class="d-flex align-items-center gap-3">
                @if (auth()->check() && !$threadDone)
                    <button aria-label="Balas komentar"
                            class="btn btn-link btn-sm p-0 text-decoration-none link-body-secondary reply-btn"
                            data-bs-target="#replyCollapse{{ $comment->id }}"
                            data-bs-toggle="collapse"
                            data-id="{{ $comment->id }}"
                            data-username="{{ $comment->user->username }}"
                            title="Balas komentar"
                            type="button">
                        <i aria-hidden="true"
                           class="bi bi-reply me-1"></i>Balas
                    </button>
                @endif

                <button aria-label="Suka komentar"
                        class="btn btn-link btn-sm p-0 text-decoration-none link-body-secondary like-btn comment-like-btn"
                        data-id="{{ $comment->id }}"
                        data-url="{{ route('community.comments.like', [$comment->thread->id, $comment->id]) }}"
                        title="Suka"
                        type="button">
                    <i aria-hidden="true"
                       class="bi {{ $comment->likes->where('user_id', auth()->id())->count() ? 'bi-hand-thumbs-up-fill text-primary' : 'bi-hand-thumbs-up' }} me-1"></i>
                    <span class="like-count">{{ $comment->likes->count() }}</span>
                </button>

                <button class="btn btn-link btn-sm p-0 text-decoration-none link-body-secondary"
                        data-bs-target="#repliesCollapse{{ $comment->id }}"
                        data-bs-toggle="collapse"
                        type="button">
                    <i aria-hidden="true"
                       class="bi bi-chat-left-text me-1"></i>
                    Lihat {{ $comment->replies->count() }} Balasan
                </button>
            </div>

            {{-- reply form (collapse) --}}
            <div class="collapse mt-3"
                 id="replyCollapse{{ $comment->id }}">
                @auth
                    <form action="{{ route('community.comments.store', [$comment->thread->id, $comment->id]) }}"
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
                                    type="submit">
                                <i aria-hidden="true"
                                   class="bi bi-send me-1"></i>Kirim Balasan
                            </button>
                        </div>
                    </form>
                @endauth
            </div>

            {{-- replies list (collapse) --}}
            <div class="collapse mt-2"
                 id="repliesCollapse{{ $comment->id }}">
                @foreach ($comment->replies as $reply)
                    @include('frontpages.threads.partials._reply', [
                        'reply' => $reply,
                        'comment' => $comment,
                        'thread' => $thread,
                    ])
                @endforeach
            </div>
        </div>
    </div>
</div>
