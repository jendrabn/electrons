<div class="comment reply mt-3 ms-4"
     data-id="{{ $reply->id }}"
     id="comment-{{ $reply->id }}">
    <div class="d-flex align-items-start">
        <img alt="{{ $reply->user->name }}"
             class="rounded-circle me-2"
             src="{{ $reply->user->avatar_url }}"
             style="width:32px;height:32px;object-fit:cover;">
        <div class="flex-grow-1">
            <div class="d-flex align-items-center mb-1">
                <div class="flex-grow-1">
                    <div class="d-flex align-items-center gap-2">
                        <a class="text-primary fw-semibold text-decoration-none"
                           href="">{{ $reply->user->name }}</a>
                        <small class="text-muted">&bullet;</small>
                        <small class="text-muted">{{ $reply->created_at->diffForHumans() }}</small>
                    </div>
                </div>

                <div class="ms-auto d-flex gap-2">
                    <button aria-label="Suka komentar"
                            class="btn btn-link btn-sm p-0 text-decoration-none like-btn reply-like-btn"
                            data-id="{{ $reply->id }}"
                            data-url="{{ route('posts.comments.like', [$reply->post->id, $reply->id]) }}"
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
                                data-url="{{ route('posts.comments.edit', [$reply->post->id, $reply->id]) }}"
                                title="Edit komentar"
                                type="button">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <button aria-label="Hapus komentar"
                                class="btn btn-link btn-sm p-0 text-decoration-none text-danger delete-btn reply-delete"
                                data-id="{{ $reply->id }}"
                                data-url="{{ route('posts.comments.destroy', [$reply->post->id, $reply->id]) }}"
                                title="Hapus komentar"
                                type="button">
                            <i class="bi bi-trash"></i>
                        </button>
                    @endif
                </div>
            </div>

            <div class="mb-2 comment-body">{{ $reply->body }}</div>
        </div>
    </div>
</div>
