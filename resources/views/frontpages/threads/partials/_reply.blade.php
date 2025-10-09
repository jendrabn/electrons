<div class="comment reply mt-3 ms-4"
     data-id="{{ $reply->id }}"
     id="comment-{{ $reply->id }}">
    <div class="d-flex align-items-start gap-2">
        <a aria-label="Profil {{ $reply->user->name }}"
           class="flex-shrink-0"
           href="{{ route('authors.show', $reply->user->username) }}">
            <img alt="{{ $reply->user->name }}"
                 class="rounded-circle border"
                 height="32"
                 loading="lazy"
                 src="{{ $reply->user->avatar_url }}"
                 style="object-fit:cover"
                 width="32">
        </a>

        <div class="flex-grow-1">
            <div class="d-flex align-items-center mb-1">
                <div class="flex-grow-1">
                    <div class="d-flex align-items-center gap-2">
                        <a class="fw-semibold text-decoration-none link-body-emphasis"
                           href="{{ route('authors.show', $reply->user->username) }}">
                            {{ $reply->user->username }}
                        </a>
                        <small class="text-body-secondary">•</small>
                        <small class="text-body-secondary">
                            {{ $reply->created_at->diffForHumans() }}
                        </small>
                    </div>
                </div>

                @php $threadDone = isset($thread) ? (bool) $thread->is_done : false; @endphp
                <div class="ms-auto d-flex gap-2">
                    @if (auth()->check() && !$threadDone)
                        <button aria-label="Balas komentar"
                                class="btn btn-link btn-sm p-0 text-decoration-none link-body-secondary reply-btn"
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
                            class="btn btn-link btn-sm p-0 text-decoration-none link-body-secondary like-btn reply-like-btn"
                            data-id="{{ $reply->id }}"
                            data-url="{{ route('community.comments.like', [$reply->thread->id, $reply->id]) }}"
                            title="Suka"
                            type="button">
                        <i
                           class="bi {{ $reply->likes->where('user_id', auth()->id())->count() ? 'bi-hand-thumbs-up-fill text-primary' : 'bi-hand-thumbs-up' }} me-1"></i>
                        <span class="like-count">{{ $reply->likes->count() }}</span>
                    </button>

                    {{-- Edit: only reply owner can edit (admin cannot edit others) --}}
                    @if (auth()->check() && auth()->id() === $reply->user_id && !$threadDone)
                        <button aria-label="Edit komentar"
                                class="btn btn-link btn-sm p-0 text-decoration-none link-body-emphasis edit-btn reply-edit"
                                data-id="{{ $reply->id }}"
                                data-url="{{ route('community.comments.edit', [$reply->thread->id, $reply->id]) }}"
                                title="Edit komentar"
                                type="button">
                            <i class="bi bi-pencil"></i>
                        </button>
                    @endif

                    {{-- Delete: reply owner OR admin can delete --}}
                    @if (auth()->check() && (auth()->id() === $reply->user_id || auth()->user()->isAdmin()) && !$threadDone)
                        <button aria-label="Hapus komentar"
                                class="btn btn-link btn-sm p-0 text-decoration-none link-danger delete-btn reply-delete"
                                data-id="{{ $reply->id }}"
                                data-url="{{ route('community.comments.destroy', [$reply->thread->id, $reply->id]) }}"
                                title="Hapus komentar"
                                type="button">
                            <i class="bi bi-trash"></i>
                        </button>
                    @endif
                </div>
            </div>

            <div class="mb-2 text-body text-break text-wrap comment-body">
                {!! linkify_mentions($reply->body) !!}
            </div>

            {{-- collapse reply form for this reply --}}
            <div class="collapse mt-2"
                 id="replyCollapse{{ $reply->id }}">
                @auth
                    <form action="{{ route('community.comments.store', [$reply->thread->id, $reply->id]) }}"
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
                                    type="submit">
                                <i class="bi bi-send me-1"></i>Kirim
                            </button>
                        </div>
                    </form>
                @endauth
            </div>
        </div>
    </div>
</div>
