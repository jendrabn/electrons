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
                    </div>

                    <div class="d-flex align-items-center gap-2">
                        <a class="text-primary fw-semibold text-decoration-none"
                           href="">{{ $comment->user->name }}</a>
                        <small class="text-muted">&bullet;</small>
                        <small class="text-muted">{{ $comment->created_at->diffForHumans() }}</small>
                    </div>
                </div>

                <div class="ms-3 d-flex gap-2">
                    @if (auth()->check() && auth()->id() === $comment->user_id)
                        <button aria-label="Edit komentar"
                                class="btn btn-link btn-sm p-0 text-decoration-none edit-btn comment-edit"
                                data-id="{{ $comment->id }}"
                                data-url="{{ route('posts.comments.edit', [$post->id, $comment->id]) }}"
                                title="Edit komentar"
                                type="button">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <button aria-label="Hapus komentar"
                                class="btn btn-link btn-sm p-0 text-decoration-none text-danger delete-btn comment-delete"
                                data-id="{{ $comment->id }}"
                                data-url="{{ route('posts.comments.destroy', [$post->id, $comment->id]) }}"
                                title="Hapus komentar"
                                type="button">
                            <i class="bi bi-trash"></i>
                        </button>
                    @endif
                </div>
            </div>

            <div class="mb-2 comment-body">{{ $comment->body }}</div>

            <div class="d-flex align-items-center gap-3">
                @if (auth()->check())
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
                        data-url="{{ route('posts.comments.like', [$post->id, $comment->id]) }}"
                        title="Suka"
                        type="button">
                    <i
                       class="bi {{ $comment->likes->where('user_id', auth()->id())->count() ? 'bi-hand-thumbs-up-fill text-primary' : 'bi-hand-thumbs-up' }}"></i>
                    <span class="like-count">{{ $comment->likes->count() }}</span>
                </button>

                <button class="btn btn-link btn-sm p-0 text-decoration-none"
                        data-bs-target="#repliesCollapse{{ $comment->id }}"
                        data-bs-toggle="collapse"
                        type="button">
                    <i class="bi bi-chat-left-text"></i> Lihat {{ $comment->replies->count() }} Balasan
                </button>
            </div>

            <div class="collapse mt-3"
                 id="replyCollapse{{ $comment->id }}">
                @auth
                    <form action="{{ route('posts.comments.store', $post->id) }}"
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

            <div class="collapse mt-2"
                 id="repliesCollapse{{ $comment->id }}">
                @foreach ($comment->replies as $reply)
                    @include('posts.partials._reply', ['reply' => $reply, 'post' => $post])
                @endforeach
            </div>
        </div>
    </div>
</div>
