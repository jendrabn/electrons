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
                        <a class="text-decoration-none fw-semibold link-body-emphasis"
                           href="{{ route('authors.show', $reply->user->username) }}">
                            {{ $reply->user->name }}
                        </a>
                        <small class="text-body-secondary">•</small>
                        <small class="text-body-secondary">
                            {{ $reply->created_at->diffForHumans() }}
                        </small>
                    </div>
                </div>

                <div class="d-flex gap-2 ms-auto">
                    <button aria-label="Suka komentar"
                            class="btn btn-link btn-sm p-0 text-decoration-none link-body-secondary like-btn reply-like-btn"
                            data-id="{{ $reply->id }}"
                            data-url="{{ route('posts.comments.like', [$reply->post->id, $reply->id]) }}"
                            title="Suka"
                            type="button">
                        <i
                           class="bi {{ $reply->likes->where('user_id', auth()->id())->count() ? 'bi-hand-thumbs-up-fill text-primary' : 'bi-hand-thumbs-up' }}"></i>
                        <span class="like-count">{{ $reply->likes->count() }}</span>
                    </button>

                    {{-- Edit: only reply owner can edit (admin cannot edit others) --}}
                    @if (auth()->check() && auth()->id() === $reply->user_id)
                        <button aria-label="Edit komentar"
                                class="btn btn-link btn-sm p-0 text-decoration-none link-body-emphasis edit-btn reply-edit"
                                data-id="{{ $reply->id }}"
                                data-url="{{ route('posts.comments.edit', [$reply->post->id, $reply->id]) }}"
                                title="Edit komentar"
                                type="button">
                            <i class="bi bi-pencil"></i>
                        </button>
                    @endif

                    {{-- Delete: reply owner OR admin can delete --}}
                    @if (auth()->check() && (auth()->id() === $reply->user_id || auth()->user()->isAdmin()))
                        <button aria-label="Hapus komentar"
                                class="btn btn-link btn-sm p-0 text-decoration-none link-danger delete-btn reply-delete"
                                data-id="{{ $reply->id }}"
                                data-url="{{ route('posts.comments.destroy', [$reply->post->id, $reply->id]) }}"
                                title="Hapus komentar"
                                type="button">
                            <i class="bi bi-trash"></i>
                        </button>
                    @endif
                </div>
            </div>

            <div class="mb-2 text-body text-break text-wrap comment-body">
                {{ $reply->body }}
            </div>
        </div>
    </div>
</div>
