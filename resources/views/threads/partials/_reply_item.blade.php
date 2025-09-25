<div class="comment reply mt-3 ms-4"
     data-id="{{ $reply->id }}"
     id="reply-{{ $reply->id }}">
    <div class="d-flex align-items-start">
        <img alt="{{ $reply->user->name }}"
             class="rounded-circle me-2"
             src="{{ $reply->user->avatar_url }}"
             style="width:32px;height:32px;object-fit:cover;">
        <div class="flex-grow-1">
            <div class="d-flex align-items-center mb-1">
                <div class="flex-grow-1">
                    <div class="d-flex align-items-center gap-2">
                    </div>

                    <div class="d-flex align-items-center gap-2">
                        <a class="text-primary text-decoration-none"
                           href="{{ route('users.show', $reply->user->id) }}">{{ $reply->user->username }}</a>
                        <small class="text-muted">•</small>
                        <small class="text-muted">{{ $reply->created_at->diffForHumans() }}</small>
                    </div>
                </div>

                <div class="ms-auto d-flex gap-2">
                    @if (auth()->check())
                        <button aria-label="Balas komentar"
                                class="btn btn-link btn-sm p-0 text-decoration-none reply-btn"
                                data-bs-target="#replyCollapse{{ $reply->id }}"
                                data-bs-toggle="collapse"
                                data-id="{{ $reply->id }}"
                                data-username="{{ $reply->user->username }}"
                                title="Balas komentar"
                                type="button">
                            <i class="bi bi-reply"></i>
                        </button>
                    @endif

                    <button aria-label="Suka komentar"
                            class="btn btn-link btn-sm p-0 text-decoration-none like-btn reply-like-btn"
                            data-id="{{ $reply->id }}"
                            data-url="{{ route('comunity.comments.like', [$reply->thread->id, $reply->id]) }}"
                            title="Suka"
                            type="button">
                        <i
                           class="bi {{ $reply->likes->where('user_id', auth()->id())->count() ? 'bi-hand-thumbs-up-fill text-primary' : 'bi-hand-thumbs-up' }}"></i>
                        <span class="like-count">{{ $reply->likes->count() }}</span>
                    </button>

                    @if (auth()->check() && auth()->id() === $reply->user_id)
                        <button aria-label="Edit komentar"
                                class="btn btn-link btn-sm p-0 text-decoration-none edit-btn reply-edit"
                                data-id="{{ $reply->id }}"
                                title="Edit komentar"
                                type="button">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <button aria-label="Hapus komentar"
                                class="btn btn-link btn-sm p-0 text-decoration-none text-danger delete-btn reply-delete"
                                data-id="{{ $reply->id }}"
                                data-url="{{ route('comunity.comments.destroy', [$reply->thread->id, $reply->id]) }}"
                                title="Hapus komentar"
                                type="button">
                            <i class="bi bi-trash"></i>
                        </button>
                    @endif
                </div>
            </div>

            <div class="mb-2 reply-body">{!! linkify_mentions($reply->body) !!}</div>

            <div class="collapse mt-2"
                 id="replyCollapse{{ $reply->id }}">
                @auth
                    <form action="{{ route('comunity.comments.store', [$reply->thread->id, $reply->parent_id]) }}"
                          class="reply-form"
                          method="POST">
                        @csrf
                        <input name="parent_id"
                               type="hidden"
                               value="{{ $reply->id }}">
                        <textarea class="form-control mb-2"
                                  name="body"
                                  placeholder="Tulis balasan..."
                                  required
                                  rows="3"></textarea>
                        <div class="text-end">
                            <button class="btn btn-primary btn-sm"
                                    type="submit"><i class="bi bi-send"></i> Kirim</button>
                        </div>
                    </form>
                @endauth
            </div>
        </div>
    </div>
</div>
